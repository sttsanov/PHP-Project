<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Article;
use ProjectBundle\Form\ArticleType;
use ProjectBundle\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use ProjectBundle\Entity\User;


class ArticleController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/article/create", name="article_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function create(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('article/create.html.twig',
            array('form' => $form->createView()));
    }

    /**
     * @Route("/article/{id}", name="article_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewArticle($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('article/article.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/edit/{id}", name="article_edit")
     * @param $id
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editArticle($id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $userAuthorId = $this->getDoctrine()->getRepository(Article::class)->find($id)->getAuthorId();
        $userAuthorName = $this->getDoctrine()->getRepository(User::class)->find($userAuthorId);
        $currentUser = $this->getUser();
        if($userAuthorName != $currentUser){
            return $this->redirect("/article/{$id}");
        }
        if($article == null){
            return $this->redirect("/");
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($article);
            $em->flush();
            return $this->redirect('/');
        }

        return $this->render(
            ':article:edit.html.twig',
            ["article" => $article, "form" => $form->createView()]
                );
    }

    /**
     * @Route("/delete/{id}", name="article_delete")
     * @param $id
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteArticle($id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $userAuthorId = $this->getDoctrine()->getRepository(Article::class)->find($id)->getAuthorId();
        $userAuthorName = $this->getDoctrine()->getRepository(User::class)->find($userAuthorId);
        $currentUser = $this->getUser();
        if($userAuthorName != $currentUser){
            return $this->redirect("/article/{$id}");
        }
        if($article == null){
            return $this->redirect("/");
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
            return $this->redirect('/');
        }

        return $this->render(
            ':article:delete.html.twig',
            ["article" => $article, "form" => $form->createView()]
        );
    }

    /**
     * @Route("/articles/{$_GET['select']}", name="article_filter")
     * @return \Symfony\Component\HttpFoundation\Response
     */
   /* public function filterArticles()
    {
        if(isset($_GET['submit'])){
            $genrePicked = $_GET['select'];

            $article = $this->getDoctrine()->getRepository(Article::class)->findAll($genrePicked);
            return $this->render(
                ':blog:index.html.twig',
                ["article" => $article]
            );
        }
    }
*/
}

