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
}
