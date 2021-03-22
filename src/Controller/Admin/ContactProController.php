<?php

namespace App\Controller\Admin;

use App\Entity\ContactPro;
use App\Repository\ContactProRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactProController extends AbstractController
{
    /**
     * @Route("/admin/contact-pro", name="admin_contact_pro")
     */
    public function index(ContactProRepository $contactProRepository): Response
    {
        $contactPros = $contactProRepository->findAll();

        return $this->render('admin/contact_pro/index.html.twig', [
            'contactPros' => $contactPros,
        ]);
    }

    /**
     * @Route("/admin/contact-pro/{id<\d+>}/delete", name="admin_contact_pro_delete")
     */
    public function delete(ContactPro $contactPro)
    {
        // 1. Supprimer l'entité
        $em = $this->getDoctrine()->getManager();
        $em->remove($contactPro);
        $em->flush();

        // 2. Flash + Redirection sur la liste
        $this->addFlash('success', "La demande a bien été supprimée.");
        return $this->redirectToRoute('admin_contact_pro');
    }
}
