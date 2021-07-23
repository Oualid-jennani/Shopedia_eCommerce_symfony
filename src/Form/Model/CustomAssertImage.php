<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class CustomAssertImage
{
    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     maxWidth = 800,
     *     minHeight = 400,
     *     maxHeight = 800,
     *     maxHeightMessage="Please provide an image with max height 800px",
     *     maxWidthMessage="Please provide an image with max  width 800px",
     *     minHeightMessage="Please provide an image with min  height 400px",
     *     minWidthMessage="Please provide an image with min  width 400px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage="Please provide a valid type: only JPEG and JPG and PNG allowed",
     * )
     */
    private $brochureProfile;

    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     maxWidth = 800,
     *     minHeight = 400,
     *     maxHeight = 800,
     *     maxHeightMessage="Please provide an image with max height 800px",
     *     maxWidthMessage="Please provide an image with max  width 800px",
     *     minHeightMessage="Please provide an image with min  height 400px",
     *     minWidthMessage="Please provide an image with min  width 400px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage="Please provide a valid type: only JPEG and JPG and PNG allowed",
     * )
     */
    private $brochureCover;

    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     maxWidth = 800,
     *     minHeight = 400,
     *     maxHeight = 800,
     *     maxHeightMessage="Please provide an image with max height 800px",
     *     maxWidthMessage="Please provide an image with max  width 800px",
     *     minHeightMessage="Please provide an image with min  height 400px",
     *     minWidthMessage="Please provide an image with min  width 400px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage="Please provide a valid type: only JPEG and JPG and PNG allowed",
     * )
     */
    private $brochureGallery;

    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     maxWidth = 800,
     *     minHeight = 400,
     *     maxHeight = 800,
     *     maxHeightMessage="Please provide an image with max height 800px",
     *     maxWidthMessage="Please provide an image with max  width 800px",
     *     minHeightMessage="Please provide an image with min  height 400px",
     *     minWidthMessage="Please provide an image with min  width 400px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage="Please provide a valid type: only JPEG and JPG and PNG allowed",
     * )
     */
    private $brochureCuisine;

    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     maxWidth = 800,
     *     minHeight = 400,
     *     maxHeight = 800,
     *     maxHeightMessage="Please provide an image with max height 800px",
     *     maxWidthMessage="Please provide an image with max  width 800px",
     *     minHeightMessage="Please provide an image with min  height 400px",
     *     minWidthMessage="Please provide an image with min  width 400px",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     *     mimeTypesMessage="Please provide a valid type: only JPEG and JPG and PNG allowed",
     * )
     */
    private $brochureMenu;

    /**
     * @return mixed
     */
    public function getBrochureProfile()
    {
        return $this->brochureProfile;
    }

    /**
     * @param mixed $brochureProfile
     */
    public function setBrochureProfile($brochureProfile): void
    {
        $this->brochureProfile = $brochureProfile;
    }

    /**
     * @return mixed
     */
    public function getBrochureCover()
    {
        return $this->brochureCover;
    }

    /**
     * @param mixed $brochureCover
     */
    public function setBrochureCover($brochureCover): void
    {
        $this->brochureCover = $brochureCover;
    }

    /**
     * @return mixed
     */
    public function getBrochureCuisine()
    {
        return $this->brochureCuisine;
    }

    /**
     * @param mixed $brochureCuisine
     */
    public function setBrochureCuisine($brochureCuisine): void
    {
        $this->brochureCuisine = $brochureCuisine;
    }

    /**
     * @return mixed
     */
    public function getBrochureGallery()
    {
        return $this->brochureGallery;
    }

    /**
     * @param mixed $brochureGallery
     */
    public function setBrochureGallery($brochureGallery): void
    {
        $this->brochureGallery = $brochureGallery;
    }

    /**
     * @return mixed
     */
    public function getBrochureMenu()
    {
        return $this->brochureMenu;
    }

    /**
     * @param mixed $brochureMenu
     */
    public function setBrochureMenu($brochureMenu): void
    {
        $this->brochureMenu = $brochureMenu;
    }

}