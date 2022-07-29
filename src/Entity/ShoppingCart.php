<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ShoppingCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingCartRepository::class)]
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
class ShoppingCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $validatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'shoppingCart', targetEntity: ProductInShoppingCart::class)]
    private Collection $productInShoppingCarts;

    #[ORM\ManyToOne(inversedBy: 'shoppingCarts')]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->productInShoppingCarts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeInterface $validatedAt): self
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, ProductInShoppingCart>
     */
    public function getProductInShoppingCarts(): Collection
    {
        return $this->productInShoppingCarts;
    }

    public function addProductInShoppingCart(ProductInShoppingCart $productInShoppingCart): self
    {
        if (!$this->productInShoppingCarts->contains($productInShoppingCart)) {
            $this->productInShoppingCarts->add($productInShoppingCart);
            $productInShoppingCart->setShoppingCart($this);
        }

        return $this;
    }

    public function removeProductInShoppingCart(ProductInShoppingCart $productInShoppingCart): self
    {
        if ($this->productInShoppingCarts->removeElement($productInShoppingCart)) {
            // set the owning side to null (unless already changed)
            if ($productInShoppingCart->getShoppingCart() === $this) {
                $productInShoppingCart->setShoppingCart(null);
            }
        }

        return $this;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

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
}
