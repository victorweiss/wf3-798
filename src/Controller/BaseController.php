<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        // Requète à la BDD
        $prenom = 'Victor';

        // ...

        $prenoms = [ 'Fred', 'Paul', 'Joe' ];

        return $this->render('base/home.html.twig', [
            'prenom' => '',
            'prenoms' => $prenoms,
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about(): Response
    {
        return $this->render('base/about.html.twig');
    }
}
