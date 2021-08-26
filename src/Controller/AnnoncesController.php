<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    #[Route('/favoris/ajout/{id}', name: 'ajout_favoris')]
    public function ajoutFavoris(Annonces $annonce)
    {
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('home');
    }
    #[Route('/favoris/retrait/{id}', name: 'retrait_favoris')]
    public function retraitFavoris(Annonces $annonce)
    {
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('home');
    }
}
