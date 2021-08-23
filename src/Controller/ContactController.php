<?php
namespace App\Controller;

use App\Form\ContactType;
use App\Service\MailerService;
use App\Service\MessageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController {

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/contact', name: 'contact')]
    public function contact(
        Request $request,
        MessageService $messageService,
        MailerService $mailerService
    ): Response
    {

        $user = $this->getUser();

        $formContact = $this->createForm(ContactType::class);
        $formContact->handleRequest($request);

        if ($formContact->isSubmitted() && $formContact->isValid()) {
            $data = $formContact->getData();

            $mailerService->send(
                "Proposer une vidéo Youtube",
                $user->getEmail(),
                "tvintelligentesite@gmail.com",
                "email/contact.html.twig",
                [
                    "name" => $data['name'],
                    "message" => $data['description'],
                    "link" => $data['link']
                ]
            );
            $messageService->addSuccess('Votre message sera examiné dans les plus brefs délais');
        }

        return $this->render('default/contact.html.twig', [
            'formContact' => $formContact->createView()
        ]);
    }

}