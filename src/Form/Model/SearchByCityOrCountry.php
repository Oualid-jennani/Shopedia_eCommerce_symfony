<?php


namespace App\Form\Model;


use App\Entity\City;
use App\Entity\Country;
use Symfony\Component\Validator\Constraints\Count;

class SearchByCityOrCountry
{
    /**
     * @var City
     */
    protected $city;

    /**
     * @var Country
     */
    protected $country;


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
    public function setCity(?City $city=null): void
    {
        $this->city = $city;
    }

    /**
     * @return Country|object|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }
    //</editor-fold>
}