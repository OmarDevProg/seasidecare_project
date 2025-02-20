<?php
namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: "Name cannot be empty.")]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: false, unique: true)]
    #[Assert\NotBlank(message: "Email cannot be empty.")]
    #[Assert\Email(message: "Please provide a valid email address.")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Password cannot be empty.")]
    #[Assert\Length(min: 6, minMessage: "Password must be at least 6 characters long.")]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Role cannot be empty.")]
    #[Assert\Choice(choices: ['admin', 'client'], message: "Role must be either 'admin' or 'client'.")]
    private ?string $role = null;

    #[ORM\Transient]
    #[Assert\EqualTo(propertyPath: "password", message: "The passwords do not match.")]
    private ?string $passwordConfirm = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $orders;

    public function __construct()
    {
        $this->role = 'client'; // Rôle par défaut
        $this->orders = new ArrayCollection();
    }

    public function getOrders(): ArrayCollection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // Remove the association
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(?string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getSalt()
    {
        // No need to provide a salt since Symfony's bcrypt or Argon2 will handle it
        return null;
    }

    public function getRoles(): array
    {
        // Return the roles including a base "ROLE_USER"
        $roles = ['ROLE_' . strtoupper($this->role), 'ROLE_USER'];
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // Erase any sensitive data like plain passwords after use
    }

    public function getUserIdentifier(): string
    {
        // Return the email as the identifier
        return (string) $this->email;
    }
}
