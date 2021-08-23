<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Repository\YoutuberRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=YoutuberRepository::class)
 * @ORM\Table(name="youtuber", indexes={@ORM\Index(columns={"name"}, flags={"fulltext"})})
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name", message="Ce nom est déjà utilisé")
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
class Youtuber
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:item:Category'])]
    private $uriimage;

    /**
     *
     * @Vich\UploadableField(mapping="youtuber_image", fileNameProperty="uriimage")
     * @var File
     * @Assert\File( mimeTypes={"image/png", "image/jpg", "image/jpeg", "image/svg+xml", "image/svg", "text/plain" })
     * @Assert\NotBlank(message="image obligatoire")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $urlchanel;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:item:Category'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="youtuber", orphanRemoval=true)
     */
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->createdAt = new \DateTime();
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

    public function getUriimage(): ?string
    {
        return $this->uriimage;
    }

    public function setUriimage(?string $uriimage): self
    {

        $this->uriimage = $uriimage;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null)
    {

        $this->imageFile = $imageFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if (null !== $imageFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime();
        }
    }

    public function getUrlchanel(): ?string
    {
        return $this->urlchanel;
    }

    public function setUrlchanel(string $urlchanel): self
    {
        $this->urlchanel = $urlchanel;

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
     * Update the updatedAt field before the update
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
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
            $post->setYoutuber($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getYoutuber() === $this) {
                $post->setYoutuber(null);
            }
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
