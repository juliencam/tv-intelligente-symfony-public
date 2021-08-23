<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Action\NotFoundAction;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email",message="Cet email est déjà utilisé")
 * @UniqueEntity("pseudonym", message="Ce pseudonyme est déjà utilisé")
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

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:item:PostComment','read:collection:Post', 'read:item:Category', 'read:item:Tag'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="Veuillez renseigner un email valide")
     * @Assert\Length(
     *      min = 4,
     *      max = 200,
     *      minMessage = "Votre email doit comporter plus de {{ limit }} caractères",
     *      maxMessage = "Votre email doit comporter moins de {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="Veuillez renseigner un email")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * the attribute is not stored in the database
     * attribute will be written. Then I will read the plainPassword attribute, encrypt
     * its contents and put the encrypted password in the password attribute which will be stored in the database.
     *
     * @var string
     * @SerializedName("password")
     * @Assert\Regex(
     * pattern = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/",
     * match=true,
     * message="Votre mot de passe doit comporter au moins huit caractères,
     * au moins une lettre, un chiffre et un caractère spécial parmi : @$!%*#?&")
     * @Assert\NotBlank(message="Veuillez renseigner un mot de passe")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Length(
     *      min = 2,
     *      max = 200,
     *      minMessage = "Votre pseudonyme doit comporter plus de {{ limit }} caractères",
     *      maxMessage = "Votre pseudonyme doit comporter moins de {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="Veuillez renseigner un pseudonyme")
     */
    #[Groups(['read:item:PostComment'])]
    private $pseudonym;


    /**
     * @ORM\Column(type="smallint", options={"default":"0"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user",cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="user",cascade={"remove"})
     */
    private $postLikes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * occurs for a given entity before the respective EntityManager persist
     * @ORM\PrePersist
     */
    public function setRolesValue()
    {
        $this->roles = ['ROLE_USER'];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    public function setPseudonym(string $pseudonym): self
    {
        $this->pseudonym = $pseudonym;

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

    /**
     * occurs for a given entity before the respective EntityManager persist
     * @ORM\PrePersist
     */
    public function setStatusValue()
    {
        $this->status = 0;
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
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
            $postLike->setUser($this);
        }

        return $this;
    }

    public function removePostLike(PostLike $postLike): self
    {
        if ($this->postLikes->removeElement($postLike)) {
            // set the owning side to null (unless already changed)
            if ($postLike->getUser() === $this) {
                $postLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of plainPassword
     */ 
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */ 
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * allows to return a string if we want to display the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->pseudonym;
    }

}
