<?php

namespace App\Controller;

use App\Form\UserRemoveType;
use App\Form\UserPasswordType;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use App\Form\UserInformationEmailType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserInformationPseudonymType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Webmozart\Assert\Assert as AssertAssert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ResetPasswordRequestRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\User;
use App\Repository\UserRepository;

class UserSpaceController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/user/space', name: 'user_space', methods: "GET")]
    #[Route('/user/space/information/pseudonym', name: 'user_space_information_pseudonym', methods: "POST")]
    #[Route('/user/space/information/email', name: 'user_space_information_email', methods: ["POST"])]
    #[Route('/user/space/password', name: 'user_space_password', methods: "POST")]
    #[Route('/user/space/remove', name: 'user_space_remove', methods: "POST")]
    public function index(Request $request,
                          EntityManagerInterface $entityManager,
                          RequestStack $requestStack,
                          UserPasswordHasherInterface $passwordEncoder,
                          ResetPasswordRequestRepository $resetPasswordRequestRepository,
                          SessionInterface $session,
                          UserRepository $userRepository
                        ): Response
    {


        $formUserPassword = $this->createForm(UserPasswordType::class, null ,[
            'action' => $this->generateUrl('user_space_password'),
            'method' => 'POST'
        ]);
        $formUserPassword->handleRequest($request);

        $formUserInformationEmail = $this->createForm(UserInformationEmailType::class, null ,[
            'action' => $this->generateUrl('user_space_information_email'),
            'method' => 'POST'
        ]);
        $formUserInformationEmail->handleRequest($request);

        $formUserInformationPseudonym = $this->createForm(UserInformationPseudonymType::class, null ,[
            'action' => $this->generateUrl('user_space_information_pseudonym'),
            'method' => 'POST'
        ]);
        $formUserInformationPseudonym->handleRequest($request);

        $formUserRemove = $this->createForm(UserRemoveType::class, null ,[
            'action' => $this->generateUrl('user_space_remove'),
            'method' => 'POST'
        ]);
        $formUserRemove->handleRequest($request);


        $user = $this->getUser();

        if ($formUserInformationEmail->isSubmitted() && $formUserInformationEmail->isValid()) {

            $requestUserInformationEmail = $request->request->get('user_information_email');

            $token = $requestUserInformationEmail['_token'];

            if (!$this->isCsrfTokenValid("task_item", $token)) {
                throw $this->createAccessDeniedException('Are you token to me !??!??');
            }

            if ($this->isCsrfTokenValid("task_item", $token)) {
                $dataUser = $formUserInformationEmail->getData();

                $verificationRequestEmail =$userRepository->findOneBy(['email' => $dataUser["email"]]);

                if (null === $verificationRequestEmail) {

                        $user->setEmail($dataUser["email"]);
                        $user->setIsVerified(false);
                        $entityManager->persist($user);
                        $entityManager->flush();

                        $this->emailVerifier->sendEmailConfirmation(
                            'app_verify_email',
                            $user,
                            (new TemplatedEmail())
                        ->from(new Address('tvintelligentesite@gmail.com', 'tvintelligentesite'))
                        ->to($user->getEmail())
                        ->subject('Confirmation Email TvIntelligente')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                        );

                        $this->addFlash('success-information', "Un email vous a été envoyé pour confirmer votre changement d'email");

                        $session = $requestStack->getSession();
                        $session->set('route_redirection_verify_email', 'user_space');
                }else {

                    if ($user->getEmail() === $dataUser["email"]) {
                        $this->addFlash('error-information-email', "Veuillez entrer un email différend de votre email actuel");
                    }elseif ($verificationRequestEmail) {
                        $this->addFlash('error-information-email', "Cet email est déjà utilisé");
                    }

                }
            }
        }

        if ($formUserInformationPseudonym->isSubmitted() && $formUserInformationPseudonym->isValid()) {

            $requestUserInformationPseudonym = $request->request->get('user_information_pseudonym');

            $token = $requestUserInformationPseudonym['_token'];

            if (!$this->isCsrfTokenValid("task_item", $token)) {
                throw $this->createAccessDeniedException('Are you token to me !??!??');
            }

            if ($this->isCsrfTokenValid("task_item", $token)) {

                $dataUser = $formUserInformationPseudonym->getData();

                $verificationRequestPseudonym = $userRepository->findOneBy(['pseudonym' => $dataUser["pseudonym"]]);

                if (null === $verificationRequestPseudonym) {
                    $user->setPseudonym($dataUser["pseudonym"]);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success-information-pseudonym', "Votre pseudonyme à bien été changé");
                }else {

                    if ($user->getPseudonym() === $dataUser["pseudonym"]) {
                        $this->addFlash('error-information-pseudonym', "Veuillez entrer un pseudonym différend de votre pseudonym actuel");
                    }elseif ($verificationRequestPseudonym) {
                        $this->addFlash('error-information-pseudonym', "Cet pseudonym est déjà utilisé");
                    }

                }
            }
        }

         if ($formUserPassword->isSubmitted() && $formUserPassword->isValid()) {

            $requestUserInformation = $request->request->get('user_password');

            $token = $requestUserInformation['_token'];

            if (!$this->isCsrfTokenValid("jhuihjnjnjn_password", $token)) {

                throw $this->createAccessDeniedException('Are you token to me !??!??');

            }
            if ($this->isCsrfTokenValid("jhuihjnjnjn_password", $token)) {

                if ($requestUserInformation['plainPassword'] === $requestUserInformation['passwordVerify']) {
                        $user->setPassword($passwordEncoder->hashPassword(
                            $user,
                            $requestUserInformation['plainPassword']
                        ));

                        $entityManager->persist($user);
                        $entityManager->flush();

                        $this->addFlash('success-password', "Le mot de passe à bien été modifié");
                } else {
                    $this->addFlash('error-password', "Le nouveau mot de passe n'est pas identique au mot de passe de confirmation");
                }

            }
         }

         if ($formUserRemove->isSubmitted() && $formUserRemove->isValid()) {

            $requestUserInformation = $request->request->get('user_remove');

            $token = $requestUserInformation['_token'];


            if (!$this->isCsrfTokenValid("oieufjfss_delete", $token)) {

                throw $this->createAccessDeniedException('Are you token to me !??!??');

            }
            if ($this->isCsrfTokenValid("oieufjfss_delete", $token)) {

                $this->get('security.token_storage')->setToken(null);

                if ($userRepository->find($user->getId())) {
                    $entityManager->remove($user);
                    $entityManager->flush();

                    $session->invalidate(0);
                    $this->addFlash('success-remove', "Votre compte à bien été supprimé");
                }
                return $this->redirectToRoute('home');


            }
         }


        return $this->render('user_space/index.html.twig', [
            'formUserInformationEmail' => $formUserInformationEmail->createView(),
            'formUserInformationPseudonym' => $formUserInformationPseudonym->createView(),
            'formUserPassword' => $formUserPassword->createView(),
            'formUserRemove' => $formUserRemove->createView()
        ]);

    }
}
