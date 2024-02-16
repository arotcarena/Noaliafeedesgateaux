<?php 
namespace App\Controller\Admin;

use App\DataModel\VisitSearchFilter;
use App\Form\VisitSearchFilterType;
use App\Repository\VisitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminVisitController extends AbstractController
{
    public function __construct(
        private VisitRepository $visitRepository
    )
    {

    }

    #[Route('/admin/visites', name: 'admin_visit_index')]
    public function index(Request $request): Response
    {
        $searchFilter = new VisitSearchFilter;
        $form = $this->createForm(VisitSearchFilterType::class, $searchFilter);
        $form->handleRequest($request);

        $pagination = $this->visitRepository->findFilteredPaginated($searchFilter, $request, 10);

        return $this->render('admin/visit/index.html.twig', [
            'form' => $form,
            'pagination' => $pagination
        ]);
    }
}