<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Form\ConsumerFormType;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConsumerController extends AbstractController
{
    #[Route('/consumer', name: 'app_consumer')]
    public function index(ConsumerRepository $repository, Security $security): Response
    {
        if($this->isGranted('ROLE_CONSUMER')){
            $user = $security->getUser();
            $id = $user->getConsumer()->getId();

            return $this->redirectToRoute('app_consumer_view',[
                'id' => $id,
            ]);
        }else if(! $this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_access_denied');
        }

        $consumers = $repository->findAll();

        return $this->render('consumer/index.html.twig', [
            'consumers' => $consumers,
        ]);
    }

    #[Route('/consumer/{id}', name: 'app_consumer_view')]
    public function view(int $id, ConsumerRepository $repository, Security $security): Response
    {
        $user = $security->getUser();

        $consumer = $repository->find($id);

        return $this->render('consumer/view.html.twig', [
            'consumer' => $consumer,
        ]);

    }

    #[Route('/new/consumer', name: 'app_consumer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consumer = new Consumer();

        $form = $this->createForm(ConsumerFormType::class, $consumer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($consumer);
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer');
        }

        return $this->render('consumer/new.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/consumer/{id}/update', name: 'app_consumer_update', methods: ['GET', 'POST'])]
    public function update(Consumer $consumer,Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsumerFormType::class, $consumer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer');
        }

        return $this->render('consumer/update.html.twig',[
            'form' => $form,
            'consumer' => $consumer,
        ]);
    }

    #[Route('/delete/consumer/{id}', name: 'app_consumer_delete', methods: ['POST'])]
    public function delete(Consumer $consumer, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($consumer);
        $entityManager->flush();

        return $this->redirectToRoute('app_consumer');
    }
}
