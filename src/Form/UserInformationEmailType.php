<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Naming\UniqidNamer;

class UserInformationEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez renseigner un email',
                        ]),
                        new Length([
                            'min' => 4,
                            'max' => 200,
                            'minMessage' => "Votre email doit comporter plus de {{ limit }} caractères",
                            'maxMessage' => 'Votre email doit comporter moins de {{ limit }} caractères',
                        ]),
                        new Email([
                            "message"=> "Veuillez renseigner un email valide"
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
