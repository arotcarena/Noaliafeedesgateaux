<?php
namespace App\Controller\Admin;

use App\Repository\CakeRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminPictureController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PictureRepository $pictureRepository,
        private CakeRepository $cakeRepository
    )
    {

    }

    #[Route('/admin/pictures/delete', name: 'admin_picture_delete')]
    public function delete(Request $request): Response
    {
        $picture = $this->pictureRepository->find($request->get('id'));
        if(!$picture)
        {
            throw new Exception('il n\'y a aucun objet Picture avec l\'id '.$request->get('id').'. Suppression impossible');
        }
        $this->em->remove($picture);
        $this->em->flush();
        return new Response(json_encode('ok'));
    }

    #[Route('/admin/pictures/replace-first', name: 'admin_picture_replaceFirst')]
    public function replaceFirstPicture(Request $request): Response
    {
        $cake = $this->cakeRepository->find($request->get('cake_id'));
        $picture = $this->pictureRepository->find($request->get('picture_id'));
        //on passe la first picture actuelle dans les pictures
        $cake->addPicture($cake->getFirstPicture());
        //puis on supprime la picture sélectionnée de la liste des pictures, et on l'ajoute comme first picture 
        $cake->removePicture($picture)
                ->setFirstPicture($picture);
        $this->em->flush();
        return new Response(json_encode('ok'));
    }
}