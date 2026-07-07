<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Entity\Order;
use App\Form\OrderFormType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $repository) : Response
    {
        $orders = $repository->findAll();

        return $this->render('order/index.html.twig',[
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_view')]
    public function view(Order $order): Response
    {
        return $this->render('order/view.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/consumer/{id}/create_order', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager) : Response
    {
        $consumer = $entityManager->getRepository(Consumer::class)->find($id);
        $order = new Order();

        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $now = new \DateTimeImmutable();
            $order->setCreatedAt($now);
            $order->setConsumer($consumer);

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer_view', [
                "id" => $consumer->getId(),
            ]);
        }

        return $this->render('order/new.html.twig',[
            'form' => $form,
            'consumer' => $consumer,
            ]);
    }
}
