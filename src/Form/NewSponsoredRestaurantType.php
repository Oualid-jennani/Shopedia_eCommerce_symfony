<?php

namespace App\Form;

use App\Entity\SponsoredRestaurant;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewSponsoredRestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',DateTimeType::class,[
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5'=>false
            ])
            ->add('endDate',DateTimeType::class,[
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5'=>false
            ])
            ->add('user',EntityType::class,[
                'class'=>User::class,
                'query_builder' => function (UserRepository $us) {
                    return $us->QueryUsersByRole("ROLE_RESTAURANT");
                },
                'choice_label' => 'restaurantName',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SponsoredRestaurant::class,
        ]);
    }
}
