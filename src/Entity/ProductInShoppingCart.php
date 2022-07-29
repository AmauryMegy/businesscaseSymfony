<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductInShoppingCartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductInShoppingCartRepository::class)]
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
class ProductInShoppingCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[
        Assert\NotNull(
            message: 'The product name must be filled.',
        ),
        Assert\GreaterThan(
            value: 0,
            message: 'The product quantity must be greater than 0.',
        ),
    ]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[
        Assert\NotNull(
            message: 'The product price must be filled.',
        ),
        Assert\GreaterThan(
            value: 0,
            message: 'The product price must be greater than 0.',
        ),
    ]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'productInShoppingCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingCart $shoppingCart = null;

    #[ORM\ManyToOne(inversedBy: 'productInShoppingCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getShoppingCart(): ?ShoppingCart
    {
        return $this->shoppingCart;
    }

    public function setShoppingCart(?ShoppingCart $shoppingCart): self
    {
        $this->shoppingCart = $shoppingCart;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
