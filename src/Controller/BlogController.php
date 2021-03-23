<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    # --- Front

    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [

        ]);
    }

    /**
     * @Route("/article", name="blog_article")
     */
    public function article(): Response
    {
        return $this->render('blog/article.html.twig', [

        ]);
    }


    # --- Back

    /**
     * @Route("/mon-espace/articles", name="membre_blog_list")
     */
    public function membreBlogList()
    {
        return $this->render('blog/membre/list.html.twig', [

        ]);
    }

    /**
     * @Route("/mon-espace/articles/nouveau", name="membre_blog_create")
     */
    public function membreBlogCreate(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', "L'article a bien été créé.");
            return $this->redirectToRoute('membre_blog_list');
        }

        return $this->render('blog/membre/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
