<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->setFormTypeOption('disabled','disabled');
        $email = EmailField::new('email');
        $roles =  ChoiceField::new('roles')->allowMultipleChoices(true)
        ->setChoices(['ADMIN' => 'ROLE_ADMIN', 'USER' => 'ROLE_USER'])->setRequired(true);
        $pseudonym = Field::new('pseudonym');
        $password = Field::new('plainPassword')->setRequired(true)->setFormType(PasswordType::class);


        if (Crud::PAGE_INDEX === $pageName) {

            return [$id, $email, $roles, $pseudonym];

        }
        elseif (Crud::PAGE_EDIT === $pageName) {

            return [$id, $email, $roles, $pseudonym, $password];

        }elseif (Crud::PAGE_NEW === $pageName) {

            return [$email, $roles, $pseudonym, $password];

        }
    }

}
