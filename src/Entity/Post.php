<?php

namespace App\Entity;


use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping\PreUpdate;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use ProxyManager\ProxyGenerator\Util\Properties;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="post", indexes={@ORM\Index(columns={"title"}, flags={"fulltext"})})
 * @ORM\HasLifecycleCallbacks()
 */
#[ApiResource(
    normalizationContext: ['groups' => ['read:collection:Post']],
    collectionOperations:['get'],
    paginationItemsPerPage: 10,
    paginationClientItemsPerPage: true,
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:collection:Post',
                'read:item:Post',
                'read:item:PostComment']]
            ],
    ]
),
]
//https://api-platform.com/docs/core/filters/
//#[ApiFilter(DateFilter::class, properties: ['createdAt' => DateFilter::INCLUDE_NULL_AFTER])]
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection:Post', 'read:item:Category','read:item:Tag'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection:Post', 'read:item:Category','read:item:Tag'])]
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:item:Post', 'read:item:Category','read:item:Tag' ])]
    private $iframe;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Youtuber::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $youtuber;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="posts")
     */
    #[Groups(['read:collection:Post', 'read:item:Tag'])]
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="posts")
     */
    #[Groups(['read:collection:Post', 'read:item:Category'])]
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
     */
    #[Groups(['read:item:PostComment'])]
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="post", orphanRemoval=true)
     */
    #[Groups(['read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $postLikes;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIframe(): ?string
    {
        return $this->iframe;
    }

    public function setIframe(string $iframe): self
    {
        $this->iframe = $iframe;

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


    public function getYoutuber(): ?Youtuber
    {
        return $this->youtuber;
    }

    public function setYoutuber(?Youtuber $youtuber): self
    {
        $this->youtuber = $youtuber;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getPostLikes(): Collection
    {
        return $this->postLikes;
    }

    public function addPostLike(PostLike $postLike): self
    {
        if (!$this->postLikes->contains($postLike)) {
            $this->postLikes[] = $postLike;
            $postLike->setPost($this);
        }

        return $this;
    }

    public function removePostLike(PostLike $postLike): self
    {
        if ($this->postLikes->removeElement($postLike)) {
            // set the owning side to null (unless already changed)
            if ($postLike->getPost() === $this) {
                $postLike->setPost(null);
            }
        }

        return $this;
    }

    /**
     * permet de savoir si cette article est liké par un User
     *
     * @param  User $user
     * @return boolean
     */
    public function isLikedByUser(User $user): bool
    {
        foreach($this->postLikes as $postLike) {

            if ($postLike->getUser() === $user) {

                return true;

            }
        }

        return false;
    }

    /**
     * allows to return a string if we want to display the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }


}
