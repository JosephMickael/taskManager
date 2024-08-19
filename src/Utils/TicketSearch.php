<?php

namespace App\Utils;

use Doctrine\Common\Collections\ArrayCollection;

class TicketSearch
{
  /**
   * @var String
   */
  private $filter;

  /**
   * Get the value of filter
   *
   * @return  String
   */
  public function getFilter()
  {
    return $this->filter;
  }

  /**
   * Set the value of filter
   *
   * @param  String  $filter
   *
   * @return  self
   */
  public function setFilter(String $filter)
  {
    $this->filter = $filter;

    return $this;
  }
}
