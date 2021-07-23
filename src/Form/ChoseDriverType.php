<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ChoseDriverType extends AbstractType
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
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository,Security $security)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var City $city */
        $city = $options['city'];
        $builder
            ->add('driver', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findDriversByCity($city),
                'required' => true,
                'choice_label' => 'fullName',
                'placeholder' => 'Chose Driver'
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // ... adding the name field if needed
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
        $resolver->setRequired([
            'city',
        ]);
    }

}
