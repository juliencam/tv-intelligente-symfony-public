<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @UniqueEntity("name", message="déjà utilisé")
 */
#[ApiResource(
    normalizationContext: ['groups' => ['read:collection:Category']],
    collectionOperations:['get'],
    paginationEnabled: false,
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:collection:Category',
                'read:item:Category'
                ],
                ]
            ],
    ]
)]

class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection:Post','read:collection:Category', 'read:item:Tag'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection:Post','read:collection:Category', 'read:item:Tag'])]
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="categories")
     */
    #[Groups(['read:item:Category'])]
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeCategory($this);
        }

        return $this;
    }

    /**
     * allows to return a string if we want to display the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}
