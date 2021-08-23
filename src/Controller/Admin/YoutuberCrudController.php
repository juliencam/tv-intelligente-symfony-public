<?php

namespace App\Controller\Admin;

use App\Entity\Youtuber;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class YoutuberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Youtuber::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

        $id = IdField::new('id')->setFormTypeOption('disabled','disabled');
        $name = TextField::new('name');
        $url = UrlField::new('urlchanel')->setLabel("URL de la chaîne de l'onglet vidéo");
        $imageFile = TextField::new('imageFile')->setFormType(VichImageType::class)->setTranslationParameters(['form.label.delete'=>'Supprimer']);
        $image = ImageField::new('uriimage')->setRequired(true)->setBasePath('/assets/images/youtuber');


        if (Crud::PAGE_INDEX === $pageName) {

            return [$id, $name, $image];

        }
        elseif (Crud::PAGE_EDIT === $pageName) {

            return [$id, $name, $url, $imageFile];

        }elseif (Crud::PAGE_NEW === $pageName) {

            return [$name, $url, $imageFile ];

        }
    }

    // public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {

    //     $entityManager->persist($entityInstance);
    //     $entityManager->flush();
    // }
    
}
