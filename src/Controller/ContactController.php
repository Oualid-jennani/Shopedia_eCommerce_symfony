<?php

namespace App\Controller;

use App\Form\ContactType;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function index(Request $request, Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class) ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $message = (new \Swift_Message($contact['subject']))
                        ->setFrom('contact@foodepia.com')
                        ->setTo('foodepia@gmail.com')
                        ->setBody($this->renderView(
                            'frontOffice/emails/contact.html.twig', compact('contact')
                        ),'text/html');
            if ($mailer->send($message)){
                $this->addFlash('success','Message Sanded') ;
            } else $this->addFlash('error','Mail error') ;
        }
        return $this->render('frontOffice/default/contact.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    
    /**
     * @Route("/testContact", name="testContact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('contact@foodepia.com')
            ->to('foodepia@gmail.com')
            ->replyTo('foodepia@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return new  JsonResponse('ok',200);
    }
}
