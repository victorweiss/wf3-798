<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\WebAuthenticator;
use App\Service\EmailService;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailService $emailService
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('redirect_user');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_MEMBER']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // do anything else you need here, like send an email

            // Envoi mail
            $emailService->send([
                'to' => $user->getEmail(),
                'subject' => "Validez votre inscription",
                'template' => 'email/security/verify_email.html.twig',
                'context' => [
                    'user' => $user
                ],
            ]);

            $this->addFlash('success', "Merci de vérifier votre compte en cliquant sur le lien dans l'email que nous vous avons envoyé.");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verification-email/{token}", name="verify_email")
     */
    public function verifyEmail(
        string $token,
        Encryptor $encryptor,
        UserRepository $userRepository,
        GuardAuthenticatorHandler $guardHandler,
        WebAuthenticator $authenticator,
        Request $request
    ){
        $id = $encryptor->decrypt($token);
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException("Votre compte n'a pas été trouvé.");
        }

        $user->setEmailVerified(true);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main' // firewall name in security.yaml
        );
    }
}
