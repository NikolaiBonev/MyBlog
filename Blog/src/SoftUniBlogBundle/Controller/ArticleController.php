<?php

namespace SoftUniBlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\User;
use SoftUniBlogBundle\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/article/create", name="article_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
                if ($article === null)
                {
                    echo "<script>alert('There is no such article');
                    window.location.href='/'</script>";
                }
                $userid = $this->getDoctrine()->getRepository(Article::class)->find($id);
        return $this->render('article/article.html.twig', ['article' => $article, 'id' => $userid]);
    }


    /**
     * @Route("/article/edit/{id}", name="article_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editArticle($id, Request $request)
    {

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($article === null)
        {
            echo "<script>alert('There is no such article');
                    window.location.href='/'</script>";
        }
        $currentUser= $this->getUser();
        if (!$currentUser->isAuthor($article) && !$currentUser->isAdmin())
        {
            echo "<script>alert('Access denied!');
                    window.location.href='/'</script>";
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted())
        {
            $data = $this->getDoctrine()->getManager();
            $data->persist($article);
            $data->flush();
            return $this->redirectToRoute('article_view', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/edit.html.twig',[
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteArticle($id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if ($article === null)
        {
            echo "<script>alert('There is no such article');
                    window.location.href='/'</script>";
        }
        $currentUser= $this->getUser();
        if (!$currentUser->isAuthor($article) && !$currentUser->isAdmin())
        {
            echo "<script>alert('Access denied!');
                    window.location.href='/'</script>";
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted())
        {
            $data = $this->getDoctrine()->getManager();
            $data->remove($article);
            $data->flush();
            return $this->redirectToRoute('blog_index', [
                'id' => $article->getId()
            ]);
        }
        return $this->render('article/delete.html.twig', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/article/confirm/delete/{id}", name="article_confirm_delete")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleConfirmDelete($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if ($article === null)
        {
            echo "<script>alert('There is no such article');
                    window.location.href='/'</script>";
        }
        $currentUser= $this->getUser();
        if (!$currentUser->isAuthor($article) && !$currentUser->isAdmin())
        {
            echo "<script>alert('Access denied!');
                    window.location.href='/'</script>";
        }
        $data = $this->getDoctrine()->getManager();
        $data->remove($article);
        $data->flush();
        return $this->redirectToRoute('blog_index');
    }
}
