<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;

class HomeController extends AbstractController
{
    /**
     * Page d'acceuil
     *
     * @return Response
     */
    public function index(EntityManagerInterface $em): Response
    {
        //Tous les articles en base de données
        $articles= $em->getRepository(Article::class)->findAll();
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
