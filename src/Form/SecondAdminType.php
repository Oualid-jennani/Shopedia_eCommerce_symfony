<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\User;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecondAdminType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var CityRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager, CityRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class)
            ->add('email',EmailType::class)
            ->add('password',PasswordType::class)
            ->add('confirm_password',PasswordType::class)
            ->add('contactPhone',TextType::class)
            ->add('fullName',TextType::class)
            ->add('country', EntityType::class, [
                'placeholder' => 'Choose a country',
                'class' => Country::class,
                'choice_label' => 'name',
                'mapped' => false,
            ])
            ->add('city', ChoiceType::class, [
                'placeholder' => 'Choose the city',
                'choices' => [],
                'empty_data' => '',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                /** @var City $city */
                $city = $this->repository->find($data['city']);
                $form = $arg->getForm();
                $form->getData()->setCity($city);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => false,
        ]);
    }
}
