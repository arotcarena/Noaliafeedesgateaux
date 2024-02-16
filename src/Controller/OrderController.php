<?php
namespace App\Controller;

use App\Config;
use App\Entity\Order;
use App\Email\OrderEmailSender;
use App\Persister\OrderPersister;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    public function __construct(
        private OrderPersister $orderPersister,
        private OrderEmailSender $orderEmailSender,
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em
    )
    {

    }

    #[Route('/order-form-submission', name: 'order_new')]
    public function new(Request $request):Response
    {
        if($this->orderRepository->count([]) >= Config::MAX_ORDERS)
        {
            $this->em->remove(
                $this->orderRepository->findOlder()
            );
            $this->em->flush();
        }
        $data = json_decode($request->getContent());
        $order = $this->orderPersister->persist($data);
        if($order)
        {
            $this->orderEmailSender->send($order);
        }
        return new Response(json_encode($order ? true: false));
    }
}