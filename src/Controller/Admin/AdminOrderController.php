<?php
namespace App\Controller\Admin;

use App\Form\OrderSearchFilterType;
use App\Repository\OrderRepository;
use App\DataModel\OrderSearchFilter;
use App\Services\DistanceCalculator;
use App\Twig\Runtime\LocationExtensionRuntime;
use App\Twig\Runtime\OrderDateFormatRuntime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em,
        private OrderDateFormatRuntime $orderDateFormater,
        private LocationExtensionRuntime $locationFormater,
        private DistanceCalculator $distanceCalculator
    )
    {

    }

    #[Route('/admin/mes-commandes', name: 'admin_order_index')]
    public function index(Request $request): Response
    {
        $orderSearchFilter = new OrderSearchFilter;
        $form = $this->createForm(OrderSearchFilterType::class, $orderSearchFilter);
        $form->handleRequest($request);

        $pagination = $this->orderRepository->findFilteredPaginated($orderSearchFilter, $request, 10);

        $countNotSeen = count($this->orderRepository->findBy(['seen' => 0]));
        $countAll = count($this->orderRepository->findAll());

        return $this->render('admin/order/index.html.twig', [
            'current_menu' => 'admin_order',
            'search_filter_form' => $form->createView(),
            'search_filter' => $orderSearchFilter,
            'pagination' => $pagination,
            'countNotSeen' => $countNotSeen,
            'countAll' => $countAll
        ]);
    }

    #[Route('/admin/order/delete', name: 'admin_order_apiDelete')]
    public function apiDelete(Request $request): Response
    {
        $orders = $this->orderRepository->findByIds(json_decode($request->getContent()));
        foreach($orders as $order)
        {
            $this->em->remove($order);
        }
        $this->em->flush();
        
        $flash = count($orders) > 1 ? 'Les '.count($orders).' commandes ont bien été supprimées !': 'La commande a bien été supprimée !';
        $this->addFlash('success', $flash);
        return new Response(json_encode('ok'));
    }

    #[Route('admin/order/markStatus', name: 'admin_order_apiMarkStatus')]
    public function apiMarkStatus(Request $request): Response
    {
        $data = json_decode($request->getContent());
        $orders = $this->orderRepository->findByIds($data->ids);
        foreach($orders as $order)
        {
            if($data->statusToMark === 'seen')
            { 
                $order->setSeen(true);
            }
            else
            {
                $order->setSeen(false);
            }
        }
        $this->em->flush();
        return new Response(json_encode('ok'));
    }

    #[Route('admin/order/showInfos', name: 'admin_order_apiShowOrder')]
    public function apiShowOrder(Request $request): Response
    {
        $order = $this->orderRepository->find(
            json_decode($request->getContent())
        );
        $showOrder = [];
        if($order)
        {
            $order->setSeen(true);
            $this->em->flush();

            $showOrder = [
                '.js-order-show-invoiceNumber' => 'Commande n° '. $order->getInvoiceNumber(),
                '.js-order-show-date' => $this->orderDateFormater->orderDateFormat($order->getCreatedAt(), true),
                '.js-order-show-name' => $order->getFullName(),
                '.js-order-show-mail' => $order->getEmail(),
                '.js-order-show-phone' => $order->getPhone(),
                '.js-order-show-city' => $this->locationFormater->format($order->getLocation()),
                '.js-order-show-message' => $order->getMessage()
            ];
        }
        return new Response(json_encode($showOrder));
    }

    #[Route('admin/order/countNotSeen', name: 'admin_order_apiCountNotSeen')]
    public function apiCountNotSeen(): Response
    {
        return new Response(json_encode(
            count($this->orderRepository->findBy(['seen' => false]))
        ));
    }

    #[Route('admin/distance-calculator', name: 'admin_apiDistanceCalculator')]
    public function apiDistanceCalculator(Request $request): Response
    {
        $data = json_decode($request->getContent());
        $distance = $this->distanceCalculator->calculate($data->x, $data->y);
        return new Response(json_encode($distance));
    }
}