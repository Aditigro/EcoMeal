<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repository) : Response
    {
        if(! $this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_access_denied');
        }

        $categories = $repository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_view', methods: ['GET'])]
    public function view(Category $category): Response
    {
        return $this->render('category/view.html.twig',[
            'category' => $category,
        ]);
    }

    #[Route('/new/category', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/new/category/{id}/update', name: 'app_category_update', methods: ['GET', 'POST'])]
    public function update(Category $category, Request $request, EntityManagerInterface $entityManager) : Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/update.html.twig',[
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route('/delete/category/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Category $category, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_category');
    }
}
