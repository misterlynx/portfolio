<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/cv", name="cv")
     */
    public function curiculum()
    {
        return $this->render('main/cv.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            //création du mail
            $email = (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to($contact->get('name')->getData())
                ->subject('Contact au sujet de votre portfolio')
                ->htmlTemplate('emails/contact_portfolio.html.twig')
                ->context([
                    'mail' => $contact->get('email')->getData(),
                    'message' => $contact->get('message')->getData()
                ]);
            //envoi du mail
            $mailer->send($email);

            // validation de l'envoi
            $this->addFlash('message', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('contact');
        }


        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
