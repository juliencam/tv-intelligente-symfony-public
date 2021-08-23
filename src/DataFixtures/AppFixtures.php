<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\PostLike;
use App\Entity\Youtuber;
use Doctrine\DBAL\Connection;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    const NB_YOUTUBER = 40;

    const NB_TAG = 15;

    const NB_CATEGORY = 8;

    const NB_POST = 50;

    const NB_USER = 200;

    const NB_POST_LIKE = 200;

    const NB_COMMENT = 100;

    /**
     * UserPasswordHasherInterface is the interface for the password encoder service.
     *
     * @var UserPasswordHasherInterfac
     */
    private $passwordHasher;

    /**
     * Connection service for MySQL
     *
     * @var Connection
     */
    private $connection;

    private $urlServer = "http://tv-intelligente-symfony.local/assets/images/";

    /**
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param Connection $connection
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, Connection $connection)
    {
        $this->passwordHasher = $passwordHasher;
        $this->connection = $connection;
    }

    /**
     * reset to zero of ID
     *
     * @return void
     */
    private function truncate()
    {
        // Disabling foreign_key constraints
        $this->connection->query('SET foreign_key_checks = 0');
        $this->connection->query('TRUNCATE TABLE category');
        $this->connection->query('TRUNCATE TABLE comment');
        $this->connection->query('TRUNCATE TABLE post');
        $this->connection->query('TRUNCATE TABLE post_category');
        $this->connection->query('TRUNCATE TABLE post_like');
        $this->connection->query('TRUNCATE TABLE post_tag');
        $this->connection->query('TRUNCATE TABLE tag');
        $this->connection->query('TRUNCATE TABLE youtuber');
        $this->connection->query('TRUNCATE TABLE user');
    }

    public function load(ObjectManager $manager)
    {

        $this->truncate();
        
        $faker = Factory::create('fr_FR');

        // allows to generate always the same data
        $faker->seed(2021);

//!------------------------------------------YOUTUBER---------------------------------------------

        $tabYoutuber = [];

        for ($i=0; $i < self::NB_YOUTUBER ; $i++) {

            $youtuber = new Youtuber();

            $youtuber->setName($faker->unique()->word());

            $youtuber->setUriimage("img-default.png");

            //$youtuber->setUrlimage( $this->urlServer . "img-default.png");

            $youtuber->setUrlchanel("https://www.youtube.com/channel/UCQgWpmt02UtJkyO32HGUASQ");

            $youtuber->setCreatedAt($faker->dateTimeBetween('-10 years', 'now', null));

            $tabYoutuber[] = $youtuber;

            $manager->persist($youtuber);


        }

//!------------------------------------------TAG---------------------------------------------

        $tabTag = [];

        for ($i=0; $i < self::NB_TAG ; $i++) {

            $tag = new Tag();

            $tag->setName($faker->unique()->word());

            $tabTag[] = $tag;

            $manager->persist($tag);
        }


//!------------------------------------------CATEGORY---------------------------------------------

        $tabCategory = [];

        for ($i=0; $i < self::NB_CATEGORY ; $i++) {

            $category = new Category();

            $category->setName($faker->unique()->word());

            $tabCategory[] = $category;

            $manager->persist($category);
        }

//!------------------------------------------Post---------------------------------------------

        $tabPost = [];

        for ($i=0; $i < self::NB_POST ; $i++) {

            $post = new Post();

            // relation avec youtuber = 40
            // post = 50
            shuffle($tabYoutuber);

            $post->setYoutuber($tabYoutuber[mt_rand(0,3)]);

            shuffle($tabTag);
            for ($k=0; $k <=mt_rand(0,4) ; $k++) {
                $post->addTag($tabTag[mt_rand(0,2)]);
            }

            shuffle($tabCategory);
            for ($j = 0; $j <= mt_rand(0,2) ; $j++) {
                $post->addCategory($tabCategory[mt_rand(0,2)]);
            }

            $post->setTitle($faker->sentence(mt_rand(4,9), true));

            $post->setIframe('<iframe src="https://www.youtube.com/embed/OAYZr1WbePk" frameborder="0" allowfullscreen></iframe>');

            //$post->setUriimage("img-default-youtube-chanel.png");

            //$post->setUrlimage($this->urlServer . "img-default-youtube-chanel.png");

            $post->setCreatedAt($faker->dateTimeBetween('-10 years', 'now', null));

            $tabPost[] = $post;

            $manager->persist($post);


        }

//!-------------------------------------------User--------------------------------------------

        $tabUser = [];

        for ($i=0; $i < self::NB_USER ; $i++) { 

            $user = new User();

            $user->setRoles(['ROLE_USER']);

            $encodedPassword = $this->passwordHasher->hashPassword($user, 'user');
            $user->setPassword($encodedPassword);

            $user->setPseudonym($faker->firstName());

            $user->setEmail($faker->unique()->email());

            if ($i > 10 && $i < 20) {

                $user->setStatus(1);

            } elseif ($i > 20 && $i < 30) {

                $user->setStatus(2);

            } else {

                $user->setStatus(0);
            }


            $user->setCreatedAt($faker->dateTimeBetween('-10 years', 'now', null));

            $tabUser[] = $user;

            $manager->persist($user);

        }

            $userAdmin = new User();

            $userAdmin->setRoles(['ROLE_ADMIN']);

            $encodedPassword = $this->passwordHasher->hashPassword($userAdmin, 'admin');
            $userAdmin->setPassword($encodedPassword);

            $userAdmin->setPseudonym("admin");

            $userAdmin->setEmail("admin@admin.com");

            $userAdmin->setStatus(0);

            $userAdmin->setCreatedAt($faker->dateTimeBetween('-9 years', 'now', null));

            $manager->persist($userAdmin);


//!-------------------------------------------PostLike--------------------------------------------

        $indexTabPost = count($tabPost);

        for ($i=0; $i < self::NB_POST_LIKE; $i++) {

            $randomNumber = mt_rand(0,1);

            if ($indexTabPost === 5) {

                $indexTabPost = count($tabPost);

            }

            $indexTabPost = $indexTabPost -1;

            if ($randomNumber === 0) {

                    $postLike = new PostLike();

                    $postLike->setUser($tabUser[$i]);

                    $postLike->setPost($tabPost[$indexTabPost]);
            }

            if ($randomNumber === 1) {

                    $postLike = new PostLike();

                    $postLike->setPost($tabPost[$indexTabPost]);
            }

            $manager->persist($postLike);

        }

//!-------------------------------------------Comment--------------------------------------------

        $indexTabPost = count($tabPost);

        for ($i=0; $i < self::NB_COMMENT; $i++) { 

            if ($indexTabPost === 10) {

                $indexTabPost = count($tabPost);

            }

            $indexTabPost = $indexTabPost -1;

            $comment = new Comment();

            $comment->setContent($faker->sentence(mt_rand(1,20)));

            if ($i > 10 && $i < 20) {

                $comment->setStatus(1);

            } else {

                $comment->setStatus(0);
            }

            shuffle($tabUser);
            $comment->setUser($tabUser[0]);

            $comment->setPost($tabPost[$indexTabPost]);

            $comment->setCreatedAt($faker->dateTimeBetween('-9 years', 'now', null));

            $manager->persist($comment);

        }

        $manager->flush();
    }
}
