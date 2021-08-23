<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @UniqueEntity("name", message="Ce tag est déjà utilisé")
 */
#[ApiResource(
    normalizationContext: ['groups' => ['read:collection:Tag']],
    collectionOperations:['get'],
    paginationEnabled: false,
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:item:Tag',
                'read:collection:Tag'
                ]]
            ],
    ]
)]

class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:collection:Tag'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:collection:Tag'])]
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="tags")
     */
    #[Groups(['read:item:Tag'])]
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
            $post->addTag($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeTag($this);
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
