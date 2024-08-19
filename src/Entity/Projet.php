<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjetRepository::class)
 */
class Projet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projets")
     */
    private $chefprojet;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projets")
     */
    private $porteur;

    /**
     * @ORM\OneToMany(targetEntity=Tache::class, mappedBy="projet")
     */
    private $taches;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="projet")
     */
    private $tickets;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getChefprojet(): ?User
    {
        return $this->chefprojet;
    }

    public function setChefprojet(?User $chefprojet): self
    {
        $this->chefprojet = $chefprojet;

        return $this;
    }

    public function getPorteur(): ?User
    {
        return $this->porteur;
    }

    public function setPorteur(?User $porteur): self
    {
        $this->porteur = $porteur;

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(Tache $tach): self
    {
        if (!$this->taches->contains($tach)) {
            $this->taches[] = $tach;
            $tach->setProjet($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): self
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getProjet() === $this) {
                $tach->setProjet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setProjet($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getProjet() === $this) {
                $ticket->setProjet(null);
            }
        }

        return $this;
    }
}
