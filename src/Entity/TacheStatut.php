<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class TacheStatut
{
  /**
   * @var ArrayCollection
   */
  private $statut;

  /**
   * Get the value of statut
   *
   * @return  ArrayCollection
   */
  public function getStatut()
  {
    return $this->statut;
  }

  /**
   * Set the value of statut
   *
   * @param  ArrayCollection  $statut
   *
   * @return  self
   */
  public function setStatut(ArrayCollection $statut)
  {
    $this->statut = $statut;

    return $this;
  }
}
