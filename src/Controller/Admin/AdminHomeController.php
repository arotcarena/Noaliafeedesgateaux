<?php 
namespace App\Controller\Admin;

use App\Persister\AdminDetailsUpdater;
use App\Services\DashboardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminHomeController extends AbstractController
{
    public function __construct(
        private DashboardService $dashboardService,
        private EntityManagerInterface $em,
        private AdminDetailsUpdater $adminDetailsUpdater
    )
    {

    }

    #[Route('/admin', name: 'admin_home')]
    public function index(): Response
    {
        $dashboard = $this->dashboardService->getDashboard();
        return $this->render('admin/home/index.html.twig', [
            'current_menu' => 'admin_home',
            'dashboard' => $dashboard
        ]);
    }

    #[Route('/admin/update-details', name: 'admin_apiUpdateDetails')]
    public function apiUpdateDetails(Request $request): Response
    {
        $data = json_decode($request->getContent());
        $success = $this->adminDetailsUpdater->update($data);
        if($success)
        {
            $this->addFlash('success', 'Les coordonnées ont été modifiées avec succès !');
        }
        else 
        {
            $this->addFlash('danger', 'Suite à un incident technique, les coordonnées n\'ont pas pu être modifiées, veuillez réessayer ultérieurement.');
        }
        return new Response(json_encode('ok'));
    }
}