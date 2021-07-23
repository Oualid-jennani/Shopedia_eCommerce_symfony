<?php

namespace App\Form;

use App\Entity\Order;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class OrderType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var CityRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager, CityRepository $repository,Security $security)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($this->security->getUser() == null){
            $builder
                ->add('fullName')
                ->add('phone',TextType::class)
            ;
        }

        $builder
            ->add('address',TextareaType::class)
            ->add('note',TextareaType::class,['required'=>false])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
