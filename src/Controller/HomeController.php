<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home', methods: "GET")]
    public function index(PostRepository $postRepository): Response
    {

        $mostRecentPost = $postRepository->findBy([],['createdAt' => 'desc'], 5);

        return $this->render('home/index.html.twig', [
            'mostRecentPost' => $mostRecentPost
        ]);
    }

    #[Route('/mentionsLegales', name: 'legal-notices', methods: "GET")]
    public function legalNotices(): Response
    {
        return $this->render('home/legal_notices.html.twig');
    }

}
