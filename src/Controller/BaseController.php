<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function header(string $routeName, ArticleRepository $articleRepository)
    {
        return $this->render('base/_header.html.twig', [
            'articles' => $articleRepository->findRecentArticles(3),
            'route_name' => $routeName,
        ]);
    }

    /**
     * @Route("/redirect-user", name="redirect_user")
     */
    public function redirectUser()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        } elseif ($this->isGranted('ROLE_MEMBER')) {
            return $this->redirectToRoute('member');
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public function changeLocale(string $locale, Request $request)
    {
        $request->getSession()->set('_locale', $locale);
        $pathHome = $this->generateUrl('home');
        $referer = $request->headers->get('referer', $pathHome);
        return $this->redirect($referer);
    }
}
