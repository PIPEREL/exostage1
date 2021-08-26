<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        $annonce = $annoncesRepository->findby(['active'=>true], ['created_at' => "desc"], 5);
        return $this->render('main/index.html.twig', [
            "annonces" => $annonce
        ]);
    }


    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
         $email = (new TemplatedEmail())
            ->from($contact->get('email')->getData())
            ->to("bastienpiperel@gmail.com")
            ->subject('contact site test')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['mail' => $contact->get('email')->getData(),'sujet' => $contact->get('sujet')->getData(),'message' => $contact->get('message')->getData()]);
            $mailer->send($email);
            $this->addFlash('message', 'Mail de contact envoyé');
            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}