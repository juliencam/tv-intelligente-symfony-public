<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->setFormTypeOption('disabled','disabled');
        $title = TextField::new('title');
        $iframe = TextareaField::new('iframe')->setLabel("URL de la vidéo")->setHelp('Click droit "Copier URL de la vidéo"');
        $youtuber = AssociationField::new('youtuber')->setRequired(true);
        $categories = AssociationField::new('categories')->setRequired(true);
        $tags = AssociationField::new('tags')->setRequired(true);


        if (Crud::PAGE_INDEX === $pageName) {

            return [$id, $title, $youtuber];

        }
        elseif (Crud::PAGE_EDIT === $pageName) {

            return [$id, $title, $iframe, $youtuber, $categories, $tags];

        }elseif (Crud::PAGE_NEW === $pageName) {

            return [$title, $iframe, $youtuber, $categories, $tags ];

        }

    }
}
