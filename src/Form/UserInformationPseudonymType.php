<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserInformationPseudonymType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudonym', TextType::class, [
                'label' => 'Pseudonyme',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un pseudonyme',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 200,
                        'minMessage' => "Votre pseudonyme doit comporter plus de {{ limit }} caractères",
                        'maxMessage' => 'Votre pseudonyme doit comporter moins de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Envoyer'
                ]
              )
        ;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'task_item',
        ]);
    }
}
