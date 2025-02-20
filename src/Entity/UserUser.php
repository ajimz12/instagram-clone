<?php

namespace App\Entity;

use App\Repository\UserUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserUserRepository::class)]
#[ORM\Table(name: 'user_user')]
class UserUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_source', referencedColumnName: 'id', nullable: false)]
    private ?User $userSource = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_target', referencedColumnName: 'id', nullable: false)]
    private ?User $userTarget = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserSource(): ?User
    {
        return $this->userSource;
    }

    public function setUserSource(?User $userSource): static
    {
        $this->userSource = $userSource;
        return $this;
    }

    public function getUserTarget(): ?User
    {
        return $this->userTarget;
    }

    public function setUserTarget(?User $userTarget): static
    {
        $this->userTarget = $userTarget;
        return $this;
    }
}
