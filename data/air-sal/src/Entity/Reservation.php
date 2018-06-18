<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salle", inversedBy="reservations")
     */
    private $Salle;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Prestations", inversedBy="reservations")
     */
    private $Prestations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservations")
     */
    private $User;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Facture", mappedBy="Reservation", cascade={"persist", "remove"})
     */
    private $facture;

    public function __construct()
    {
        $this->Prestations = new ArrayCollection();
        $this->User = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSalle(): ?Salle
    {
        return $this->Salle;
    }

    public function setSalle(?Salle $Salle): self
    {
        $this->Salle = $Salle;

        return $this;
    }

    /**
     * @return Collection|Prestations[]
     */
    public function getPrestations(): Collection
    {
        return $this->Prestations;
    }

    public function addPrestation(Prestations $prestation): self
    {
        if (!$this->Prestations->contains($prestation)) {
            $this->Prestations[] = $prestation;
        }

        return $this;
    }

    public function removePrestation(Prestations $prestation): self
    {
        if ($this->Prestations->contains($prestation)) {
            $this->Prestations->removeElement($prestation);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->contains($user)) {
            $this->User->removeElement($user);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(Facture $facture): self
    {
        $this->facture = $facture;

        // set the owning side of the relation if necessary
        if ($this !== $facture->getReservation()) {
            $facture->setReservation($this);
        }

        return $this;
    }
}
