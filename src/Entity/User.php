<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    itemOperations: [
        'get' => [
            'access_control' => 'is_granted("ROLE_STATS") or is_granted("ROLE_ADMIN")',
        ],
    ],
    collectionOperations: [
        'get' => [
            'access_control' => 'is_granted("ROLE_STATS") or is_granted("ROLE_ADMIN")',
        ],
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[
        Assert\NotBlank(
            message: 'The email must be filled.',
        ),
        Assert\Email(
            message: 'The email must be a valid email address.',
        ),
        Assert\Length(
            max: 180,
            maxMessage: 'The email must be at most {{ limit }} characters long.',
        ),
    ]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[
        Assert\NotBlank(
            message: 'The password must be filled.',
        ),
        Assert\Length(
            min: 8,
            minMessage: 'The password must be at least {{ limit }} characters long.',
        ),
        Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            message: 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ),
    ]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[
        Assert\NotBlank(
            message: 'The first name must be filled.',
        ),
        Assert\Regex(
            pattern: '/^[a-zA-Z]+$/',
            message: 'The first name must be only alphabetic characters without special characters.',
        ),
        Assert\Length(
            max: 255,
            maxMessage: 'The first name must be at most {{ limit }} characters long.',
        ),
    ]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[
        Assert\NotBlank(
            message: 'The last name must be filled.',
        ),
        Assert\Regex(
            pattern: '/^[a-zA-Z]+$/',
            message: 'The last name must be only alphabetic characters without special characters.',
        ),
        Assert\Length(
            max: 255,
            maxMessage: 'The last name must be at most {{ limit }} characters long.',
        ),
    ]
    private ?string $lastName = null;

    #[ORM\Column(length: 10)]
    #[
        Assert\NotBlank(
            message: 'The phone number must be filled.',
        ),
        Assert\Regex(
            pattern: '/^\d{10}$/',
            message: 'The phone number must be a ten number.',
        ),
    ]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[
        Assert\NotBlank(
            message: 'The birth date must be filled.',
        ),
        Assert\Date(
            message: 'The birth date must be a valid date.',
        ),
        Assert\Expression(
            'this.getBirthAt() < this.getRegisterAt()',
            message: 'Birth date must be lesser than register at.',
        )
    ]
    private ?\DateTimeInterface $birthAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[
        Assert\NotBlank(
            message: 'The creation date must be filled.',
        ),
        Assert\Date(
            message: 'The creation date must be a valid date.',
        ),
    ]
    private ?\DateTimeInterface $registerAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ShoppingCart::class)]
    private Collection $shoppingCarts;

    #[ORM\Column(length: 180, nullable: true, unique: true)]
    private ?string $username = null;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->shoppingCarts = new ArrayCollection();
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBirthAt(): ?\DateTimeInterface
    {
        return $this->birthAt;
    }

    public function setBirthAt(\DateTimeInterface $birthAt): self
    {
        $this->birthAt = $birthAt;

        return $this;
    }

    public function getRegisterAt(): ?\DateTimeInterface
    {
        return $this->registerAt;
    }

    public function setRegisterAt(\DateTimeInterface $registerAt): self
    {
        $this->registerAt = $registerAt;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShoppingCart>
     */
    public function getShoppingCarts(): Collection
    {
        return $this->shoppingCarts;
    }

    public function addShoppingCart(ShoppingCart $shoppingCart): self
    {
        if (!$this->shoppingCarts->contains($shoppingCart)) {
            $this->shoppingCarts->add($shoppingCart);
            $shoppingCart->setUser($this);
        }

        return $this;
    }

    public function removeShoppingCart(ShoppingCart $shoppingCart): self
    {
        if ($this->shoppingCarts->removeElement($shoppingCart)) {
            // set the owning side to null (unless already changed)
            if ($shoppingCart->getUser() === $this) {
                $shoppingCart->setUser(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
