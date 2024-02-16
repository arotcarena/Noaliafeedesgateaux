<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserExistType;
use App\DataModel\UserExist;
use App\Form\PasswordCreateType;
use App\DataModel\PasswordCreate;
use App\Services\PasswordInitService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class SecurityController extends AbstractController
{
    public function __construct(
        private PasswordInitService $passwordInitService,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $em
    )
    {

    }

    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->getUser())
        {
            if($this->isGranted('ROLE_ADMIN'))
            {
                return $this->redirectToRoute('admin_home');
            }
            return $this->redirectToRoute('home');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout()
    {
        
    }

    #[Route('/connexion/mot-de-passe-oublié', name: 'security_forgottenPassword')]
    public function forgottenPassword(Request $request): Response
    {
        $userExist = new UserExist;
        $form = $this->createForm(UserExistType::class, $userExist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->passwordInitService->requireInit($userExist->email);
            $this->addFlash('success', 'Un lien permettant de réinitialiser le mot de passe vous a été envoyé à l\'adresse "'.$userExist->email.'". Attention, le lien est valide pendant 24h.');
        }

        return $this->render('security/forgotten_password.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/connexion/réinitialiser-mon-mot-de-passe', name: 'security_passwordInit')]
    public function passwordInit(Request $request)
    {
        $user = $this->passwordInitService->verifyToken($request);
        if(!$user)
        {
            $this->addFlash('danger', 'Le lien utilisé est invalide ou périmé');
            return $this->redirectToRoute('security_login');
        }

        $passwordCreate = new PasswordCreate;
        $form = $this->createForm(PasswordCreateType::class, $passwordCreate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                $this->hasher->hashPassword($user, $passwordCreate->newPassword)
            );
            $this->em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour !');
            $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $user->getEmail());
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/password_init.html.twig', [
            'form' => $form
        ]);
    }
}
