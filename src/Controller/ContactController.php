<?php

namespace App\Controller;

use App\Service\EmailService;
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
            $data = [
                'firstname' => $request->request->get('firstname'),
                'lastname' => $request->request->get('lastname'),
                'company' => $request->request->get('company'),
                'mail' => $request->request->get('email'),
                'subject' => $request->request->get('subject'),
                'message' => $request->request->get('message'),
            ];

            // Envoyé à l'admin
            $sentToAdmin = $emailService->send([
                'replyTo' => $data['mail'],
                'subject' => '[CONTACT PRO] - ' . $data['subject'],
                'template' => 'email/contact_pro.html.twig',
                'context' => $data,
            ]);

            // Accusé de réception
            $sentToContact = $emailService->send([
                'to' => $data['mail'],
                'subject' => "Merci de nous avoir contacté",
                'template' => 'email/contact_pro_confirmation.html.twig',
                'context' => $data
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
