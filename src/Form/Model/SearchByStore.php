<?php

namespace App\Form\Model;

use App\Entity\User;
use PhpParser\Node\Scalar\String_;


class SearchByStore
{
    protected $username ;

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username ;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username):void {
        $this->username = $username;
    }
}