<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\SubCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProductType extends AbstractType
{
    /**
     * @var SubCategoryRepository
     */
    private $subCategoryRepository;

    public function __construct(SubCategoryRepository $subCategoryRepository)
    {
        $this->subCategoryRepository = $subCategoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $category */
        $category = $options['category'];
        $builder
            ->add('name',TextType::class)
            ->add('description',TextareaType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => 'Chose Product Category'
            ])
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'choices' => $this->subCategoryRepository->findByCategory($category),
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => 'Chose Sub Category'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                /** @var SubCategory $subCategory */
                $subCategory = $this->subCategoryRepository->find($data['subCategory']);
                $form = $arg->getForm();
                $form->getData()->setSubCategory($subCategory);
            });

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => false,
        ]);
        $resolver->setRequired([
            'category',
        ]);
    }
}