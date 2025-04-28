<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;


class ArticleController extends AbstractController
{
    /**
     *
     * @param   int $id Identifiant de l'article
     *
     * @return Response
     */
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)->find($id);
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
        ]);
    }


/**
 *Modifier/ Ajouter un article
 */

    public function edit(Request $request, int $id, EntityManagerInterface $em):Response
{
    if($id){
        $mode = 'update';
        $article = $em->getRepository(Article::class)->find($id);
    }
    else{
        $mode = 'new';
        $article = new Article();
    }

    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
        $this->saveArticle($article, $mode, $em);

        return $this->redirectToRoute('article_edit',array('id' => $article->getId()));
    }

    $parameters = array(
        'form' => $form->createView(),
        'article' => $article,
        'mode' => $mode
    );
    return $this->render('article/edit.html.twig', $parameters);
}

/**
 * Supprimer un article
 */
public function remove(int $id, EntityManagerInterface $em):Response
{
    $article = $em->getRepository(Article::class)->find($id);

    //L'article est supprimé
    $em->remove($article);
    $em->flush();

    return $this->redirectToRoute('homepage');
}

/**
 *Compléter l'article avec des informations avant enregistrement
 *
 * @param Article $article
 * @param string $mode
 *
 * @return Article
 */
private function completeArticleBeforeSave(Article $article, string $mode) {
    if($article->getIsPublished()){
        $article->setPublishedAt(new \DateTime());
    }
    $article->setAuthor($this->getUser());

    return $article;
}

/**
 * Enregistrer un article en base de données
 *
 * @param Article $article
 * @param string$mode
 */
private function saveArticle(Article $article, string $mode, EntityManagerInterface $em){
    $article = $this->completeArticleBeforeSave($article, $mode);

    $em->persist($article);
    $em->flush();
}
}