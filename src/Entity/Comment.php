<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
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
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item:PostComment'])]
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Content obligatoire")
     */
    #[
        Groups(['read:item:PostComment']),
        Length(min:3)
        ]
    private $content;

    /**
     * @ORM\Column(type="smallint", options={"default":"0"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="user obligatoire")
     */
    #[Groups(['read:item:PostComment'])]
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Post obligatoire")
     */
    private $post;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:item:PostComment'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Groups(['read:item:PostComment'])]
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * permet de savoir si ce commentaires appartient par un User
     *
     * @param  User $user
     * @return boolean
     */
    public function isCommentedByUser(User $user): bool
    {

            if ($this->user === $user) {

                return true;

        }

        return false;
    }

}
