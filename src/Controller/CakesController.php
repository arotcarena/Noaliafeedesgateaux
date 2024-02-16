<?php
namespace App\Controller;

use App\Config;
use DateTimeZone;
use DateTimeImmutable;
use App\Services\VisitCounter;
use App\Converter\CakeConverter;
use App\Repository\CakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CakesController extends AbstractController 
{
    public function __construct(
        private CakeRepository $cakeRepository,
        private VisitCounter $visitCounter,
        private EntityManagerInterface $em,
        private CakeConverter $cakeConverter
    )
    {

    }
    #[Route('/toutes-mes-créations', name: 'cakes_index')]
    public function index(): Response
    {
        $this->visitCounter->count('cakes_index');
        $limit = Config::CAKES_INDEX_INFINITE_PAGINATION_LIMIT;
        $cakes = $this->cakeRepository->findCurrents($limit);
        return $this->render('cakes/index.html.twig', [
            'current_menu' => 'cakes_index',
            'cakes' => $cakes,
            'limit' => $limit
        ]);
    }

    #[Route('/api/cakes/getCakeData', name: 'cakes_api_getCakeData')]
    public function getCakeData(Request $request): Response 
    {
        $cake = $this->cakeRepository->find(
            (int)(json_decode($request->getContent()))
        );
        return new Response($this->cakeConverter->getImageShowDataToJson($cake));
    }

    #[Route('/api/cakes/addVisit', name: 'cakes_api_addVisit')]
    public function apiAddVisit(Request $request): Response 
    {
        $cake = $this->cakeRepository->find(
            (int)(json_decode($request->getContent()))
        );
        $cake->addVisit();
        $cake->setLastVisit(new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')));
        $this->em->flush();
        return new Response(json_encode('ok'));
    }

    #[Route('/api/cakes/infinitePagination', name: 'cakes_api_infinitePagination')]
    public function infinitePagination(Request $request)
    {
        $limit = Config::CAKES_INDEX_INFINITE_PAGINATION_LIMIT;
        $offset = json_decode($request->getContent())->offset ?? null;
        if($offset !== null) 
        {
            $cakes = $this->cakeRepository->findCurrents($limit, $offset);
            if(count($cakes) > 0) {
                return new Response(json_encode([
                    'isEmpty' => false,
                    'isLast' => (count($cakes) < $limit),
                    'newOffset' => ($offset + count($cakes)),
                    'items' => $this->cakeConverter->getInfinitePaginationGroupData($cakes)
                ]));
            }
        }
        return new Response(json_encode([
            'isEmpty' => true
        ]));
    }
}