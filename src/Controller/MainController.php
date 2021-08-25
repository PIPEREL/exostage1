<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        $annonce = $annoncesRepository->findby(['active'=>true], ['created_at' => "desc"]);
        return $this->render('main/index.html.twig', [
            "annonces" => $annonce
        ]);
    }
}
