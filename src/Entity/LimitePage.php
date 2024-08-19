<?php

namespace App\Entity;

class LimitePage
{
  /**
   * @var int
   */
  private $nombre;

  /**
   * Get the value of nombre
   *
   * @return  int
   */
  public function getNombre()
  {
    return $this->nombre;
  }

  /**
   * Set the value of nombre
   *
   * @param  int  $nombre
   *
   * @return  self
   */
  public function setNombre(int $nombre)
  {
    $this->nombre = $nombre;

    return $this;
  }
}
