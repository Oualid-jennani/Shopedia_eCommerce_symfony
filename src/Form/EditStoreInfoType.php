<?php

namespace App\Form;

use App\Entity\ServiceType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditStoreInfoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('storeName',TextType::class)
            ->add('storePhone',TextType::class)
            ->add('fullName',TextType::class)
            ->add('contactPhone',TextType::class)
            ->add('email',EmailType::class)
            ->add('state',TextType::class)
            ->add('address',TextareaType::class)
            ->add('postCode',TextType::class)
            ->add('username',TextType::class)
            ->add('localisation',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
