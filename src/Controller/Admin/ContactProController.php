<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactProController extends AbstractController
{
    /**
     * @Route("/admin/contact-pro", name="admin_contact_pro")
     */
    public function index(): Response
    {
        return $this->render('admin/contact_pro/index.html.twig', [

        ]);
    }
}
