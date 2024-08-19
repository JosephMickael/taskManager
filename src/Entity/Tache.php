<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tache
 *
 * @ORM\Table(name="tache", indexes={@ORM\Index(name="IDX_93872075F6203804", columns={"statut_id"})})
 * @ORM\Entity
 */
class Tache
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_ech_user", type="datetime", nullable=true)
     */
    private $dateEchUser;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_ech_chef", type="datetime", nullable=true)
     */
    private $dateEchChef;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_ech_porteur_projet", type="datetime", nullable=true)
     */
    private $dateEchPorteurProjet;

    /**
     * @var \Statut
     *
     * @ORM\ManyToOne(targetEntity="Statut")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="statut_id", referencedColumnName="id")
     * })
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=Projet::class, inversedBy="taches")
     */
    private $projet;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="taches")
     */
    private $developpeur;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateterminer;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="taches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="tache")
     */
    private $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateEchUser(): ?\DateTimeInterface
    {
        return $this->dateEchUser;
    }

    public function setDateEchUser(?\DateTimeInterface $dateEchUser): self
    {
        $this->dateEchUser = $dateEchUser;

        return $this;
    }

    public function getDateEchChef(): ?\DateTimeInterface
    {
        return $this->dateEchChef;
    }

    public function setDateEchChef(?\DateTimeInterface $dateEchChef): self
    {
        $this->dateEchChef = $dateEchChef;

        return $this;
    }

    public function getDateEchPorteurProjet(): ?\DateTimeInterface
    {
        return $this->dateEchPorteurProjet;
    }

    public function setDateEchPorteurProjet(?\DateTimeInterface $dateEchPorteurProjet): self
    {
        $this->dateEchPorteurProjet = $dateEchPorteurProjet;

        return $this;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getDeveloppeur(): ?User
    {
        return $this->developpeur;
    }

    public function setDeveloppeur(?User $developpeur): self
    {
        $this->developpeur = $developpeur;

        return $this;
    }

    public function getDateterminer(): ?\DateTimeInterface
    {
        return $this->dateterminer;
    }

    public function setDateterminer(?\DateTimeInterface $dateterminer): self
    {
        $this->dateterminer = $dateterminer;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setTache($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getTache() === $this) {
                $notification->setTache(null);
            }
        }

        return $this;
    }
}
