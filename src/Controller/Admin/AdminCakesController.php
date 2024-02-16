<?php
namespace App\Controller\Admin;

use Error;
use Exception;
use App\Config;
use DateTimeZone;
use App\Entity\Cake;
use App\Form\CakeType;
use DateTimeImmutable;
use App\Form\SearchFilterType;
use App\DataModel\SearchFilter;
use App\Form\CakeSearchFilterType;
use App\Repository\CakeRepository;
use App\DataModel\CakeSearchFilter;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\FormException\UploadException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCakesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CakeRepository $cakeRepository
    )
    {

    }

    #[Route('/admin/mes-gâteaux', name: 'admin_cakes_index')]
    public function index(Request $request): Response
    {
        $searchFilter = new CakeSearchFilter;
        $form = $this->createForm(CakeSearchFilterType::class, $searchFilter);
        $form->handleRequest($request);
        $pagination = $this->cakeRepository->findFilteredPaginated($searchFilter, $request, 6);
        [$countSpotlightedOnSearch, $countAllOnSearch] = $this->cakeRepository->countSpotlightedAndAll($searchFilter);
        return $this->render('admin/cakes/index.html.twig', [
            'current_menu' => 'admin_cakes',
            'pagination' => $pagination,
            'search_filter' => $searchFilter,
            'count_spotlighted_on_search' => $countSpotlightedOnSearch,
            'count_all_on_search' => $countAllOnSearch
        ]);
    }

    #[Route('/admin/mes-gâteaux/gâteau-n°{id}', name: 'admin_cakes_show')]
    public function show(Cake $cake): Response
    {
        return $this->render('admin/cakes/show.html.twig', [
            'current_menu' => 'admin_cakes',
            'cake' => $cake
        ]);
    }

    #[Route('/admin/mes-gâteaux/gâteau-n°{id}/modifier', name: 'admin_cakes_update')]
    public function update(Cake $cake, Request $request): Response
    {
        $form = $this->createForm(CakeType::class, $cake);

        try 
        {
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $this->em->flush();
                $this->addFlash('success', 'Le gâteau a bien été modifié !');
                return $this->redirectToRoute('admin_cakes_show', ['id' => $cake->getId()]);
            }
        } 
        catch(Exception $e) 
        {
            $form->get('pictureFiles')->addError(new FormError($e->getMessage()));
        }
        

        return $this->render('admin/cakes/update.html.twig', [
            'current_menu' => 'admin_cakes',
            'cake' => $cake,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/mes-gâteaux/supprimer', name: 'admin_cakes_delete')]
    public function delete(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-cake', $request->request->get('token'))) {
            throw new Exception('invalid token');
        }

        $id = $request->request->get('cake_id');
        $cake = $this->cakeRepository->find($id);
        $title = $cake->getTitle();
        $this->em->remove($cake);
        $this->em->flush();
        $this->addFlash('success', 'Le gâteau "'. ($title ?: 'Sans titre') .'" a bien été supprimé !');
        return $this->redirectToRoute('admin_cakes_index');
    }

    #[Route('/admin/mes-gâteaux/ajouter', name: 'admin_cakes_new')]
    public function new(Request $request): Response
    {
        $cake = new Cake;
        $form = $this->createForm(CakeType::class, $cake, [
            'firstPicture' => true
        ]);
        
        try 
        {
            $form->handleRequest($request);

            //on met ça ici pour afficher cette erreur même s'il y a d'autres erreurs
            if($form->isSubmitted() && !$cake->getFirstPicture())
            {
                throw new UploadException('La photo principale est obligatoire', 'firstPictureFile');
            }

            if($form->isSubmitted() && $form->isValid()) 
            {
                $cake->setCreatedAt(new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')));
                if($this->cakeRepository->count([]) >= Config::MAX_CAKES)
                {
                    $this->addFlash('danger', 'Impossible d\'ajouter plus de ' .Config::MAX_CAKES. ' gâteaux. Veuillez d\'abord supprimer un gâteau existant');
                    return $this->redirectToRoute('admin_cakes_index');
                }
                $this->em->persist($cake);
                $this->em->flush();
                $this->addFlash('success', 'Le gâteau a bien été ajouté !');
                return $this->redirectToRoute('admin_cakes_show', ['id' => $cake->getId()]);
            }
        } 
        catch(UploadException $e) 
        {
            $form->get($e->field)->addError(new FormError($e->getMessage()));
        }
        

        return $this->render('admin/cakes/new.html.twig', [
            'current_menu' => 'admin_cakes',
            'form' => $form->createView()
        ]);
    }


    #[Route('/admin/cakes/findTitlesByQ', name: 'admin_cakes_findTitlesByQ')]
    public function findTitlesByQ(Request $request): Response
    {
        $q = str_replace('+', ' ', $request->get('q'));
        $limit = $request->get('limit', 5);
        $titles = $this->cakeRepository->findTitlesByQ($q, $limit);
        return new Response(json_encode($titles));
    }

    #[Route('admin/cakes/spotlightToggle/{id}', name: 'admin_cakes_spotlightToggle')]
    public function spotlightToggle(Cake $cake): Response 
    {
        if($cake->isSpotlighted())
        {
            $cake->setSpotlighted(false);
        }
        else
        {
            if($this->cakeRepository->fullSpotlighted())
            {
                return new Response(json_encode([
                    'ok' => false, 
                    'message' => 'Impossible de mettre plus de '.Config::MAX_SPOTLIGHTED.' gâteaux à la Une !'
                ]));
            }
            $cake->setSpotlighted(true);
        }
        $this->em->flush();
        return new Response(json_encode([
            'ok' => true,
            'message' => $this->cakeRepository->countSpotlighted()
        ]));
    }
}