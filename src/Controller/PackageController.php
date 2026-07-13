<?php

namespace App\Controller;

use App\Dto\PackageSearchFilter;
use App\Entity\Business;
use App\Entity\Order;
use App\Entity\Package;
use App\Form\PackageFiltersFormType;
use App\Form\PackageFormType;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PackageController extends AbstractController
{
    #[Route('/package', name: 'app_package')]
    public function index(Request $request, PackageRepository $repository): Response
    {
        $filters = new PackageSearchFilter();
        $form = $this->createForm(PackageFiltersFormType::class, $filters);
        $form->handleRequest($request);

        $packages = $repository->findByFilter($filters);

        return $this->render('package/index.html.twig', [
            'packages' => $packages,
            'form' => $form,
        ]);
    }
    #[Route('/package/{id}', name: 'app_package_view')]
    public function view(Package $package, Security $security) : Response
    {
        $editable = true;
        if($this->isGranted('ROLE_CONSUMER')){
            $editable = false;
        }else if($this->isGranted('ROLE_BUSINESS')){
            $user = $security->getUser();
            $id = $user->getBusiness()->getId();

            $editable = ($id == $package->getBusiness()->getId());
        }
        return $this->render('package/view.html.twig', [
            'package' => $package,
            'editable' => $editable,
        ]);
    }
    #[Route('/business/{id}/create_package', name: 'app_package_new', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $business = $entityManager->getRepository(Business::class)->find($id);

        $package = new Package();

        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $now = new \DateTimeImmutable();
            $package->setCreatedAt($now);
            $package->setBusiness($business);

            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/thumbnails/' . $business->getId();
                // Move the file to the directory where images are stored
                try {
                    $photoFile->move($uploadDir, $newFilename);
                } catch (FileException $e) {
                    dd('Error uploading file');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $package->setPhoto('/uploads/thumbnails/' . $business->getId() . '/' . $newFilename);
            }

            $entityManager->persist($package);
            $entityManager->flush();

            return $this->redirectToRoute('app_business_view', [
                'id' => $business->getId(),
            ]);
        }

        return $this->render('package/new.html.twig',[
            'form' => $form,
            'business' => $business,
        ]);
    }

    #[Route('/package/{id}/update', name: 'app_package_update', methods: ['GET', 'POST'])]
    public function update(SluggerInterface $slugger, Package $package, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();
            $business = $package->getBusiness();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/thumbnails/' . $business->getId();
                // Move the file to the directory where images are stored
                try {
                    $photoFile->move($uploadDir, $newFilename);
                } catch (FileException $e) {
                    dd('Error uploading file');
                }

                $package->setPhoto('/uploads/thumbnails/' . $business->getId() . '/' . $newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_package');
        }

        return $this->render('package/update.html.twig',[
            'form' => $form,
            'package' => $package,
        ]);
    }

    #[Route('/delete/package/{id}', name: "app_package_delete", methods: ['POST'])]
    public function delete(Package $package, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($package);
        $entityManager->flush();

        return $this->redirectToRoute('app_package');
    }
}
