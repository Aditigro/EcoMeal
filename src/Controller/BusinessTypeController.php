<?php

namespace App\Controller;

use App\Entity\BusinessType;
use App\Form\BusinessTypeFormType;
use App\Repository\BusinessTypeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BusinessTypeController extends AbstractController
{
    #[Route('/business_type', 'app_business_type')]
    public function index(BusinessTypeRepository $repository): Response
    {
        if(! $this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_access_denied');
        }

        $businessTypes = $repository->findAll();

        return $this->render('businessType/index.html.twig', [
            'businessTypes' => $businessTypes,
        ]);
    }

    #[Route('/business_type/{id}', 'app_business_type_view', methods: ['GET'])]
    public function view(int $id, BusinessTypeRepository $repository): Response{

        $businessType = $repository->find($id);

        return $this->render('businessType/view.html.twig', [
            'businessType' => $businessType,
        ]);
    }

    #[Route('/new/business_type', name: 'app_business_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager) : Response{
        $businessType = new BusinessType();

        $form = $this->createForm(BusinessTypeFormType::class, $businessType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($businessType);
            $entityManager->flush();

            return $this->redirectToRoute('app_business_type');
        }

        return $this->render('businessType/new.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/business_type/{id}/update', name: 'app_business_type_update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface$entityManager, BusinessType $businessType) : Response
    {
        $form = $this->createForm(BusinessTypeFormType::class, $businessType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('app_business_type');
        }

        return $this->render('businessType/update.html.twig',[
            'form' => $form,
            'business_type' => $businessType,
        ]);
    }

    #[Route('/delete/business_type/{id}', name: 'app_business_type_delete', methods: ['POST'])]
    public function delete(BusinessType $businessType, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($businessType);
        $entityManager->flush();

        return $this->redirectToRoute('app_business_type');
    }

}
