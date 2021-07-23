<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"},message="Email already used")
 * @UniqueEntity(fields={"email"},message="username already used")
 */

class User implements UserInterface ,\Serializable
{
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function serialize()
    {
        // TODO: Implement serialize() method.
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
        list($this->id, $this->username, $this->password,) = unserialize($serialized);
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true , unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "Your password must be less than {{limit}} characters"
     * )
     */
    private $password;

    /**
     * @Assert\EqualTo(
     *     propertyPath="password",
     *     message="Vous n'avez pas tapé le meme mot de passe"
     * )
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactPhone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $storePhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $storeName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localisation;

    /**
     * @ORM\OneToOne(targetEntity=SponsoredRestaurant::class, mappedBy="user")
     */
    private $sponsoredRestaurant;

    /**
     * @ORM\OneToMany(targetEntity=Reviews::class, mappedBy="user")
     */
    private $reviews;

    /**
     * @ORM\OneToOne(targetEntity=Transportation::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $transportation;


    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="user")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="customer")
     */
    private $orderCustomer;
    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="driver")
     */
    private $orderDriver;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="store")
     */
    private $cartStore;


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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profileImage;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverImage;

    //------------------ Menage Roles-----------------
    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity=Gallery::class, mappedBy="store", cascade={"persist", "remove"})
     */
    private $gallery;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $occupied;

    /**
     * @ORM\OneToMany(targetEntity=Follower::class, mappedBy="customer")
     */
    private $followers;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="users")
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="store")
     */
    private $categories;


    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    //------------------ Menage Roles-----------------

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->orderCustomer = new ArrayCollection();
        $this->orderDriver = new ArrayCollection();
        $this->cartStore = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getStorePhone(): ?string
    {
        return $this->storePhone;
    }

    public function setStorePhone(?string $storePhone): self
    {
        $this->storePhone = $storePhone;

        return $this;
    }

    public function getStoreName(): ?string
    {
        return $this->storeName;
    }

    public function setStoreName(?string $storeName): self
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }
    
    public function getSponsoredRestaurant(): ?SponsoredRestaurant
    {
        return $this->sponsoredRestaurant;
    }

    public function setSponsoredRestaurant(?SponsoredRestaurant $sponsoredRestaurant): self
    {
        // unset the owning side of the relation if necessary
        if ($sponsoredRestaurant === null && $this->sponsoredRestaurant !== null) {
            $this->sponsoredRestaurant->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($sponsoredRestaurant !== null && $sponsoredRestaurant->getUser() !== $this) {
            $sponsoredRestaurant->setUser($this);
        }

        $this->sponsoredRestaurant = $sponsoredRestaurant;

        return $this;
    }

    /**
     * @return Collection|Reviews[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    public function getTransportation(): ?Transportation
    {
        return $this->transportation;
    }

    public function setTransportation(?Transportation $transportation): self
    {
        // unset the owning side of the relation if necessary
        if ($transportation === null && $this->transportation !== null) {
            $this->transportation->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($transportation !== null && $transportation->getUser() !== $this) {
            $transportation->setUser($this);
        }

        $this->transportation = $transportation;

        return $this;
    }


    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrderCustomer(): Collection
    {
        return $this->orderCustomer;
    }

    public function addOrderCustomer(Order $orderCustomer): self
    {
        if (!$this->orderCustomer->contains($orderCustomer)) {
            $this->orderCustomer[] = $orderCustomer;
            $orderCustomer->setCustomer($this);
        }

        return $this;
    }

    public function removeOrderCustomer(Order $orderCustomer): self
    {
        if ($this->orderCustomer->removeElement($orderCustomer)) {
            // set the owning side to null (unless already changed)
            if ($orderCustomer->getCustomer() === $this) {
                $orderCustomer->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrderDriver(): Collection
    {
        return $this->orderDriver;
    }

    public function addOrderDriver(Order $orderDriver): self
    {
        if (!$this->orderDriver->contains($orderDriver)) {
            $this->orderDriver[] = $orderDriver;
            $orderDriver->setDriver($this);
        }

        return $this;
    }

    public function removeOrderDriver(Order $orderDriver): self
    {
        if ($this->orderDriver->removeElement($orderDriver)) {
            // set the owning side to null (unless already changed)
            if ($orderDriver->getDriver() === $this) {
                $orderDriver->setDriver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCartStore(): Collection
    {
        return $this->cartStore;
    }

    public function addCartStore(Cart $cartStore): self
    {
        if (!$this->cartStore->contains($cartStore)) {
            $this->cartStore[] = $cartStore;
            $cartStore->setStore($this);
        }

        return $this;
    }

    public function removeCartStore(Cart $cartStore): self
    {
        if ($this->cartStore->removeElement($cartStore)) {
            // set the owning side to null (unless already changed)
            if ($cartStore->getStore() === $this) {
                $cartStore->setStore(null);
            }
        }

        return $this;
    }

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

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    public function setGallery(Gallery $gallery): self
    {
        // set the owning side of the relation if necessary
        if ($gallery->getRestaurant() !== $this) {
            $gallery->setRestaurant($this);
        }

        $this->gallery = $gallery;

        return $this;
    }

    public function getOccupied(): ?bool
    {
        return $this->occupied;
    }

    public function setOccupied(?bool $occupied): self
    {
        $this->occupied = $occupied;

        return $this;
    }

    /**
     * @return Collection|Follower[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(Follower $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
            $follower->setCustomer($this);
        }

        return $this;
    }

    public function removeFollower(Follower $follower): self
    {
        if ($this->followers->removeElement($follower)) {
            // set the owning side to null (unless already changed)
            if ($follower->getCustomer() === $this) {
                $follower->setCustomer(null);
            }
        }

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setStore($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getStore() === $this) {
                $category->setStore(null);
            }
        }

        return $this;
    }

}