<?php


namespace App\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;

class AssertProductImage
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
    private $brochure;

    /**
     * @return mixed
     */
    public function getBrochure()
    {
        return $this->brochure;
    }

    /**
     * @param mixed $brochure
     */
    public function setBrochure($brochure): void
    {
        $this->brochure = $brochure;
    }
}