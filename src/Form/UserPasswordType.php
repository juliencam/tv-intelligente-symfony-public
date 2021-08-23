<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Nouveau Mot de passe'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe',
                    ]),
                    new Regex(
                        "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/",
                        "Votre mot de passe doit comporter au moins huit caractères,
                        au moins une lettre, un chiffre et un caractère spécial parmi : @$!%*#?&"
                        )
                ],
            ])
            ->add('passwordVerify', PasswordType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Confirmer Mot de passe'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe',
                    ]),
                    new Regex(
                        "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/",
                        "Votre mot de passe doit comporter au moins huit caractères,
                        au moins une lettre, un chiffre et un caractère spécial parmi : @$!%*#?&"
                        )
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Modifier mon mot de passe'
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
            'csrf_token_id'   => 'jhuihjnjnjn_password',
        ]);
    }
}
