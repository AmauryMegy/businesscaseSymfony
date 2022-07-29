<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PaymentMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
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
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Assert\NotBlank(
            message: 'The payment method name must be filled.',
        ),
        Assert\Length(
            max: 255,
            maxMessage: 'The payment method name must be at most {{ limit }} characters long.',
        ),
    ]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'paymentMethod', targetEntity: ShoppingCart::class)]
    private Collection $shoppingCarts;

    public function __construct()
    {
        $this->shoppingCarts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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
            $shoppingCart->setPaymentMethod($this);
        }

        return $this;
    }

    public function removeShoppingCart(ShoppingCart $shoppingCart): self
    {
        if ($this->shoppingCarts->removeElement($shoppingCart)) {
            // set the owning side to null (unless already changed)
            if ($shoppingCart->getPaymentMethod() === $this) {
                $shoppingCart->setPaymentMethod(null);
            }
        }

        return $this;
    }
}
