<?php

namespace App\Form\Model;

use App\Entity\City;


class SearchByCity
{
    /**
     * @var City
     */
    protected $city;


    /**
     * @return City|object|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city): void
    {
        $this->city = $city;
    }

    //</editor-fold>


}