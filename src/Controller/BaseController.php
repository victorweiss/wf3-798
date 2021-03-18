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

        $produit = [ 'nom' => 'Mon produit', 'prix' => 9.99, 'stock' => 0 ];

        return $this->render('base/home.html.twig', [
            'prenom' => '',
            'prenoms' => $prenoms,
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about(): Response
    {
        return $this->render('base/about.html.twig');
    }

    public function header(string $routeName)
    {
        $articles = [
            [ 'titre' => 'Article 1' ],
            [ 'titre' => 'Article 2' ],
            [ 'titre' => 'Article 3' ],
        ];

        return $this->render('base/_header.html.twig', [
            'articles' => $articles,
            'route_name' => $routeName,
        ]);
    }
}
