<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Consumer;
use App\Entity\User;
use App\Form\BusinessRegistrationFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator , Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $consumer= new Consumer();
        $user->setConsumer($consumer);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setRoles(['ROLE_CONSUMER']);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $loginUrl = $urlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new Email())
                ->from('ecomeal@example.com')
                ->to($user->getEmail())
                ->subject('Successful Registration')
                ->html(sprintf('<p>Welcome to our application. Link to <a href="%s">log in</a></p>', $loginUrl));

            // puteti folosi si template-uri pentru mesaje.
            //            ->htmlTemplate('emails/welcome.html.twig')
            //            ->context([
            //                'userName' => $userName,
            //                'loginUrl' => $loginUrl,
            //            ]);

            $mailer->send($email);

            $security->login($user, 'App\Security\LoginFormAuthenticator', 'main');
            return $this->redirectToRoute('app_consumer');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/register/business', name: 'app_register_business')]
    public function registerBusiness(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(BusinessRegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setRoles(['ROLE_BUSINESS']);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);

            $entityManager->flush();

            // do anything else you need here, like send an email

            $security->login($user, 'App\Security\LoginFormAuthenticator', 'main');
            return $this->redirectToRoute('app_business');
        }

        return $this->render('registration/register_business.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
