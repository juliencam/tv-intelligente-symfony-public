<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->setFormTypeOption('disabled','disabled');
        $content = TextareaField::new('content');
        $user = AssociationField::new('user')->setRequired(true);
        $post = AssociationField::new('post')->setRequired(true);



        if (Crud::PAGE_INDEX === $pageName) {

            return [$id, $user, $post, $content];

        }
        elseif (Crud::PAGE_EDIT === $pageName) {

            return [$id, $content, $user, $post];

        }elseif (Crud::PAGE_NEW === $pageName) {

            return [$content, $user, $post ];

        }

    }

}
