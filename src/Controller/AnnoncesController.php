<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AnnoncesController extends AbstractController
{
    #[Route('/annonces/details/{slug}', name: 'annonce_details')]
    public function detail($slug, AnnoncesRepository $annoncesRepository): Response
    {
        $annonce = $annoncesRepository->findOneby(['slug'=> $slug]);
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonces trouvées');
        }


        return $this->render('annonces/details.html.twig', compact('annonce')); 
        
    }
}
