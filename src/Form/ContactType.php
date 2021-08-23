<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\Contact;
use App\Validator\Constraints\IsGmail;
use Symfony\Component\Form\AbstractType;
use App\Validator\Constraints\IsGmailValidator;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Url;

class ContactType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', TextType::class, [
                'label' => 'Nom de le chaîne Youtube',
                'attr' => [
                    'placeholder' => 'Nom de le chaîne Youtube',
                    'class' => 'form-control',
                    ],
                'required' => true,
                'constraints' => [
                        new Length([
                            'min' => 1,
                            'minMessage' => 'Le nom de le chaîne Youtube doit comporter plus de {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 300,
                            'maxMessage' => 'Le nom de le chaîne Youtube doit comporter moins de {{ limit }} caractères',
                        ]),
                        new NotBlank([
                            'message' => 'Veuillez entrer un nom de chaîne Youtube',
                        ]),
                    ],
            ])
            ->add('link', UrlType::class, [
                'label' => 'Url de la vidéo',
                'attr' => [
                    'placeholder' => 'Url de la vidéo',
                    'class' => 'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'minMessage' => "L'url de la vidéo doit comporter plus de {{ limit }} caractères",
                        'max' => 800,
                        'maxMessage' => "L'url de la vidéo doit comporter moins de {{ limit }} caractères",
                    ]),
                    new NotBlank([
                        'message' => "Veuillez entrer l'Url de la vidéo",
                    ]),
                    new Url([], 'Veuillez entrer une URL valide et commençant par : http ou https', ["http", "https"], true)
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Message',
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Envoyer'
                ]
              )
        ;
    }
}