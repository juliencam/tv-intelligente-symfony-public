<?php

  namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

  class EasyAdminSubscriber implements EventSubscriberInterface
  {

      private $entityManager;
      private $passwordEncoder;


      public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
      {
          $this->entityManager = $entityManager;
          $this->passwordEncoder = $passwordEncoder;

      }

      public static function getSubscribedEvents()
      {
          return [
              BeforeEntityPersistedEvent::class => ['add'],
              BeforeEntityUpdatedEvent::class => ['update'], //surtout utile lors d'un reset de mot passe plutôt qu'un réel update, car l'update va de nouveau encrypter le mot de passe DEJA encrypté ...
            ];
      }

      public function add(BeforeEntityPersistedEvent $event)
      {
          $entity = $event->getEntityInstance();

            if (!($entity instanceof Post || $entity instanceof User )) {
                return;
                }

            if (($entity instanceof Post )) {
                    $this->setPost($entity);
                }

            if (($entity instanceof User )) {
                $this->setPassword($entity);
                }

      }

      public function update(BeforeEntityUpdatedEvent $event)
      {
            $entity = $event->getEntityInstance();

            if (!($entity instanceof Post || $entity instanceof User )) {
                return;
                }

            if (($entity instanceof Post )) {


                    if (false === stristr($entity->getIframe(), 'iframe')) {

                        $this->setPost($entity);

                    }else {

                        return;
                    }

                }

            if (($entity instanceof User )) {
                $this->setPassword($entity);
                }
      }

      /**
       * @param User $entity
       */
      public function setPassword(User $entity): void
      {
          $plainPassword = $entity->getPlainPassword();

          $entity->setPassword(
              $this->passwordEncoder->hashPassword(
                  $entity,
                  $plainPassword
              )
          );
          $this->entityManager->persist($entity);
          $this->entityManager->flush();
      }

      public function setPost(Post $post)
      {

            $originalUrl = $post->getIframe();

            $arrayUrl = explode("/",$originalUrl );

            $urlPart = end($arrayUrl);

            $templateIframe = '<iframe src="https://www.youtube.com/embed/search" frameborder="0" allowfullscreen></iframe>';

            $iFrame = str_replace("search", $urlPart, $templateIframe);

            $post->setIframe($iFrame);

            $this->entityManager->persist($post);
            $this->entityManager->flush();
      }

  }