<?php

namespace App\Controller;

use App\Entity\Business;
use App\Form\BusinessFormType;
use App\Repository\BusinessRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BusinessController extends AbstractController
{
    #[Route('/business', name: 'app_business', methods: ['GET'])]
    public function index(BusinessRepository $businessRepository, Security $security): Response
    {
        if($this->isGranted('ROLE_BUSINESS')){
            $user = $security->getUser();
            $id = $user->getBusiness()->getId();

            return $this->redirectToRoute('app_business_view',[
                'id' => $id,
            ]);
        }else if(! $this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_access_denied');
        }

        $businesses = $businessRepository->findAll();

        return $this->render('business/index.html.twig', [
            'businesses' => $businesses,
        ]);
    }
    #[Route('/business/{id}', name: 'app_business_view')]
    public function view(Business $business) : Response{

        return $this->render('business/view.html.twig', [
            'business' => $business,
        ]);
    }

    #[Route('/new/business', name: 'app_business_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response{
        $business = new Business();

        $form = $this->createForm(BusinessFormType::class, $business);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($business);
            $entityManager->flush();

            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/new.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/business/{id}/update', name: 'app_business_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $business = $entityManager->getRepository(Business::class)->find($id);
        $form = $this->createForm(BusinessFormType::class, $business);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/update.html.twig',[
            'form' => $form,
            'business' => $business,
        ]);
    }

    #[Route('/delete/business/{id}', name: 'app_business_delete', methods: ['POST'])]
    public function delete(Business $business, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($business);
        $entityManager->flush();

        return $this->redirectToRoute('app_business');
    }
}
