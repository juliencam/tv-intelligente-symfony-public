<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Youtuber;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tv Intelligente Symfony');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Post', 'fa fa-file-text', Post::class);
        yield MenuItem::linkToCrud('Category', 'fa fa-file-text', Category::class);
        yield MenuItem::linkToCrud('Youtuber', 'fa fa-file-text', Youtuber::class);
        yield MenuItem::linkToCrud('Tag', 'fa fa-file-text', Tag::class);
        yield MenuItem::linkToCrud('Comment', 'fa fa-file-text', Comment::class);
        yield MenuItem::linkToCrud('User', 'fa fa-file-text', User::class);
    }
}
