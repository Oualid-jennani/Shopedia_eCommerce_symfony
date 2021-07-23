<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProductType extends AbstractType
{

    /**
     * @var SubCategoryRepository
     */
    private $subCategoryRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Security
     */
    private $security;


    public function __construct(SubCategoryRepository $subCategoryRepository,CategoryRepository $categoryRepository,Security $security)
    {
        $this->subCategoryRepository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $category */
        $category = $options['category'];

        if($category == null){
            $builder
                ->add('images', FileType::class, [
                    'attr' =>
                        [
                            'label' => 'Brochure (Image file)',
                            'accept'=>'image/*'
                        ],

                    'required' => false,
                    'multiple'=>true
                ]);

            $category = $this->categoryRepository->findOneByStore($this->security->getUser());
        }

        $builder
            ->add('name',TextType::class)
            ->add('description',TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => true,
                'choice_label' => 'name',
            ])
            ->add('subCategory', EntityType::class, [
                'class' => SubCategory::class,
                'choices' => $this->subCategoryRepository->findByCategory($category),
                'choice_label' => 'name',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                /** @var SubCategory $subCategory */
                if(isset($data['subCategory'])){
                    $subCategory = $this->subCategoryRepository->find($data['subCategory']);
                    $form = $arg->getForm();
                    $form->getData()->setSubCategory($subCategory);
                }
            });
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
