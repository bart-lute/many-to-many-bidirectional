<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Form\ArticleType;
use AppBundle\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Debug;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->findBy(array(), array('title' => 'ASC'));

        $tags = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findBy(array(), array('name' => 'ASC'));

        return $this->render('@App/default/home.html.twig', array(
            'articles' => $articles,
            'tags' => $tags,
        ));
    }

    /**
     * @Route("/article/new", name="article_new")
     */
    public function newArticleAction(Request $request)
    {
        $article = new Article();
        $article->setTitle('New article');

        $form = $this->createForm(new ArticleType(), $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('homepage');

        }

        return $this->render('@App/default/article.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/article/edit/{id}", name="article_edit")
     */
    public function editArticleAction(Request $request, $id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No Article found with id ' . $id
            );
        }

        $form = $this->createForm(new ArticleType(), $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/default/article.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/tag/new", name="tag_new")
     */
    public function newTagAction(Request $request)
    {
        $tag = new Tag();
        $tag->setName('Foo');

        $form = $this->createForm(new TagType(), $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/default/tag.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/tag/edit/{id}", name="tag_edit")
     */
    public function editTagAction(Request $request, $id)
    {
        /**
         * @var Tag $tag
         */
        $tag = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->find($id);

        if (!$tag) {
            throw $this->createNotFoundException(
                'No Tag found with id ' . $id
            );
        }

        $form = $this->createForm(new TagType(), $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tag = $form->getData();
            $tag->resetArticles();

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/default/tag.html.twig', array(
            'form' => $form->createView(),
        ));

    }


}
