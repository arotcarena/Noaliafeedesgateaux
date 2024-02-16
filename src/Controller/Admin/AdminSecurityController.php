<?php
namespace App\Controller\Admin;

use App\DataModel\PasswordUpdate;
use App\Entity\User;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminSecurityController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('admin/sécurité/modifier-mon-mot-de-passe', name: 'admin_security_passwordUpdate')]
    public function passwordUpdate(Request $request)
    {
        $passwordUpdate = new PasswordUpdate;
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User */
            $user = $this->getUser();
            $user->setPassword(
                $this->hasher->hashPassword($user, $passwordUpdate->newPassword)
            );
            $this->em->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié !');
            return $this->redirectToRoute('admin_home');
        }
        return $this->render('admin/security/password_update.html.twig', [
            'form' => $form
        ]);
    }
}