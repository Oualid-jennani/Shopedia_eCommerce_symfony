<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SubCategoryType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Security
     */
    private $security;


    public function __construct(CategoryRepository $categoryRepository,Security $security)
    {
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choices' => $this->categoryRepository->findByStore($this->security->getUser()),
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => 'Chose Category'
            ])
            ->add('name',TextType::class)
            ->add('brochure', FileType::class, [
                'label' => 'Brochure (Image file)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}
