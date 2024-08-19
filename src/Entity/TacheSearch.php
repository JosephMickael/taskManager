<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class TacheSearch
{

  /**
   * @var ArrayCollection
   */
  private $user;

  public function __construct()
  {
    $this->user = new ArrayCollection();
  }

  /**
   * Get the value of user
   *
   * @return  ArrayCollection
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Set the value of user
   *
   * @param  ArrayCollection  $user
   *
   * @return  self
   */
  public function setUser(ArrayCollection $user)
  {
    $this->user = $user;

    return $this;
  }
}
