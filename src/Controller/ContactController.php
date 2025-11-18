<?php

namespace App\Controller;

use App\Entity\ContactRequest;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $contactRequest = new ContactRequest();
        $form = $this->createForm(ContactType::class, $contactRequest);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRequest->setSubmittedAt(new \DateTime());
            $contactRequest->setStatus('new');

            $em->persist($contactRequest);
            $em->flush();

            // Note: Email sending will be added when Mailer is configured
            // For now, just store the request in database

            $this->addFlash('success', 'Votre message a bien été envoyé. Je vous recontacterai rapidement.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
