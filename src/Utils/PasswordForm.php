<?php

namespace App\Utils;

class PasswordForm
{
  /**
   * @var string
   */
  private $oldPassword;

  /**
   * @var string
   */
  private $newPassword;

  /**
   * Get the value of oldPassword
   *
   * @return  string
   */
  public function getOldPassword()
  {
    return $this->oldPassword;
  }

  /**
   * Set the value of oldPassword
   *
   * @param  string  $oldPassword
   *
   * @return  self
   */
  public function setOldPassword(string $oldPassword)
  {
    $this->oldPassword = $oldPassword;

    return $this;
  }

  /**
   * Get the value of newPassword
   *
   * @return  string
   */
  public function getNewPassword()
  {
    return $this->newPassword;
  }

  /**
   * Set the value of newPassword
   *
   * @param  string  $newPassword
   *
   * @return  self
   */
  public function setNewPassword(string $newPassword)
  {
    $this->newPassword = $newPassword;

    return $this;
  }
}
