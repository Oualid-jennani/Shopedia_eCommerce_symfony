<?php


namespace App\Form;


use App\Entity\City;
use App\Entity\Country;
use App\Form\Model\SearchByCityOrCountry;
use App\Repository\CityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchByCityOrCountryType extends  AbstractType
{
    /**
     * @var CityRepository
     */
    private $repository;

    public function __construct(?CityRepository $repository=null)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('country', EntityType::class, [
            'placeholder' => 'Choose a country',
            'class' => Country::class,
            'choice_label' => 'name'
        ])
            ->add('city', ChoiceType::class, [
                'placeholder'=>'Choose a city',
                'required'=> false,
                'choices' => [],
                'empty_data' => '',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $arg) {
                $data = $arg->getData();
                if (isset($data['city'])) {
                    /** @var City $city */
                    $city = $this->repository->find($data['city']);
                    $form = $arg->getForm();
                    $form->getData()->setCity($city);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchByCityOrCountry::class,
            "validation_groups"=>false
        ]);
    }

}