<?php

namespace App\Controller;

use App\Entity\ContactPro;
use App\Service\EmailService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, EmailService $emailService): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if ($email && $message) {
                $sent = $emailService->send([
                    'replyTo' => $email,
                    'subject' => "CONTACT DU SITE",
                    'template' => 'email/contact.html.twig',
                    'context' => [
                        'mail' => $email,
                        'message' => $message,
                    ]
                ]);

                if ($sent) {
                    $this->addFlash('success', "<b>Merci !</b> Nous avons bien reçu votre message.");
                    return $this->redirectToRoute('contact');
                } else {
                    $this->addFlash('danger', "Le mail n'a malheureusement pas pu être envoyé :(");
                }

            } else {
                $this->addFlash('danger', "Le formulaire contient des erreurs");
            }
        }

        return $this->render('contact/index.html.twig', [

        ]);
    }

    /**
     * @Route("/contact-pro", name="contact_pro")
     */
    public function contactPro(Request $request, EmailService $emailService)
    {
        if ($request->isMethod('POST')) {
            $contactPro = (new ContactPro())
                ->setFirstname($request->request->get('firstname'))
                ->setLastname($request->request->get('lastname'))
                ->setCompany($request->request->get('company'))
                ->setEmail($request->request->get('email'))
                ->setSubject($request->request->get('subject'))
                ->setMessage($request->request->get('message'))
                ->setSentAt(new DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($contactPro);
            $em->flush();

            // Envoyé à l'admin
            $sentToAdmin = $emailService->send([
                'replyTo' => $contactPro->getEmail(),
                'subject' => '[CONTACT PRO] - ' . $contactPro->getSubject(),
                'template' => 'email/contact_pro.html.twig',
                'context' => [ 'contactPro' => $contactPro ],
            ]);

            // Accusé de réception
            $sentToContact = $emailService->send([
                'to' => $contactPro->getEmail(),
                'subject' => "Merci de nous avoir contacté",
                'template' => 'email/contact_pro_confirmation.html.twig',
                'context' => [ 'contactPro' => $contactPro ],
            ]);

            if ($sentToAdmin && $sentToContact) {
                $this->addFlash('success', "Merci de nous avoir contacté");
                return $this->redirectToRoute('contact_pro');
            } else {
                $this->addFlash('danger',"Une erreur est survenue pendant l'envoi d'email");
            }
        }

        return $this->render('contact/contact_pro.html.twig', [

        ]);
    }
}
