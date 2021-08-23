<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostLikeRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PostLikeRepository::class)
 */
#[ApiResource(
    collectionOperations:[
        'get' => [
            'controller' => NotFoundAction::class,
            'openapi_context' => [
                'summary' => 'hidden',
            ],
        ]
    ],
    itemOperations:[
        'get' => [
            'controller' => NotFoundAction::class,
            'openapi_context' => [
                'summary' => 'hidden',
            ],
        ]
    ]
)]
class PostLike
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="postLikes")
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postLikes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
