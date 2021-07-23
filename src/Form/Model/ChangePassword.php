<?php

namespace App\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;


class ChangePassword
{
    /**
     * @var string
     * @SecurityAssert\UserPassword(message = "The old password is not correct")
     */
    protected $oldPassword;

    /**
     * @var string
     * @Assert\Length(
     *     min="6",
     *     minMessage="Please provide a password with at leat {{ limit }} chars."
     * )
     */
    protected $newPassword;

    /**
     * @var string
     * @Assert\EqualTo(
     *     propertyPath="newPassword",
     *     message="The password does not match."
     * )
     */
    protected $confirmNewPassword;

    //<editor-fold desc="Getters and Setters">
    /**
     * @return string
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string
     */
    public function getConfirmNewPassword(): ?string
    {
        return $this->confirmNewPassword;
    }

    /**
     * @param string $confirmNewPassword
     */
    public function setConfirmNewPassword(string $confirmNewPassword): void
    {
        $this->confirmNewPassword = $confirmNewPassword;
    }
    //</editor-fold>


}