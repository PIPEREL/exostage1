<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnonceContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;

class AnnoncesController extends AbstractController
{
    #[Route('/annonces/details/{slug}', name: 'annonce_details')]
    public function detail($slug, AnnoncesRepository $annoncesRepository, Request $request, MailerInterface $mailer): Response
    {
        $annonce = $annoncesRepository->findOneby(['slug'=> $slug]);
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonces trouvées');
        }

        $form = $this->createForm(AnnonceContactType::class);

        $contact = $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $email = (new TemplatedEmail())
            ->from($contact->get('email')->getData())
            ->to($annonce->getUsers()->getEmail())
            ->subject('contact au sujet de votre annonce')
            ->htmlTemplate('emails/contact_annonce.html.twig')
            ->context(['annonce'=> $annonce, "mail" => $contact->get('email')->getData(), 'message' => $contact->get('message')->getData()]);
            $mailer->send($email);
            $this->addFlash('message', "votre email a bien été envoyé");
            
            return $this->redirectToRoute('annonce_details', ['slug' => $annonce->getSlug()]);
        }

        return $this->render('annonces/details.html.twig', ["annonce" => $annonce, "form" => $form->createView()] ); 
        
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
