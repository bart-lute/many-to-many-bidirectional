<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Form\ArticleType;
use AppBundle\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
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

        return $this->render('@App/default/new.article.html.twig', array(
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

            foreach ($tag->getArticles() as $article) {
                $article->addTag($tag);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/default/new.tag.html.twig', array(
            'form' => $form->createView(),
        ));
    }


}
