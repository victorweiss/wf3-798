<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\UploadService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    # --- Front

    /**
     * @Route("/blog", name="blog")
     */
    public function index(
        ArticleRepository $articleRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {
        $articles = $paginator->paginate(
            $articleRepository->findBlogArticles($request->query->all()),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="blog_article")
     */
    public function article(Article $article, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', "Merci pour votre commentaire");
            return $this->redirectToRoute('blog_article', [ 'id' => $article->getId() ]);
        }

        return $this->render('blog/article.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }


    # --- Back

    /**
     * @Route("/mon-espace/articles", name="membre_blog_list")
     * @IsGranted("ROLE_USER")
     */
    public function membreBlogList(ArticleRepository $articleRepository)
    {
        // $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->isGranted('ROLE_ADMIN')) {
            $articles = $articleRepository->findAll();
        } else {
            $articles = $articleRepository->findUserArticles($this->getUser());
        }

        return $this->render('blog/membre/list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/mon-espace/articles/nouveau", name="membre_blog_create")
     * @IsGranted("ROLE_MEMBER", message="Seuls les membres peuvent ??crire des articles")
     */
    public function membreBlogCreate(Request $request)
    {
        $article = (new Article())->setUser($this->getUser());
        return $this->handleForm($article, $request, true);
    }

    /**
     * @Route("/mon-espace/articles/modifier/{id}", name="membre_blog_update")
     * @IsGranted("ROLE_USER")
     */
    public function membreBlogUpdate(Article $article, Request $request)
    {
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $article);
        return $this->handleForm($article, $request, false);
    }

    public function handleForm(Article $article, Request $request, bool $new)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $fileName = $this->uploadService->uploadImage($image, $article);
                $article->setImage($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', "L'article a bien ??t?? " . ($new ? 'cr????' : 'modifi??'));
            return $this->redirectToRoute('membre_blog_update', [ 'id' => $article->getId() ]);
        }

        return $this->render('blog/membre/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'new' => $new
        ]);
    }

    /**
     * @Route("/mon-espace/articles/supprimer/{id}", name="membre_blog_remove")
     * @IsGranted("ROLE_USER")
     */
    public function membreBlogRemove(Article $article)
    {
        $this->denyAccessUnlessGranted(ArticleVoter::DELETE, $article, "Arr??tez d'essayer de supprimer les articles des autres, vous allez vous faire bannir !");
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', "L'article a bien ??t?? supprim??");
        return $this->redirectToRoute('membre_blog_list');
    }
}
