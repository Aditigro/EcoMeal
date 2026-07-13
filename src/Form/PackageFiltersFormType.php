<?php

namespace App\Form;

use App\Dto\PackageSearchFilter;
use App\Entity\Business;
use App\Entity\BusinessType;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Package;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackageFiltersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', SearchType::class, [
                'required' => false,
                'label' => "Name"
            ])
            ->add('minPrice', NumberType::class, [
                'required' => false,
                'label' => "Min Price",
            ])
            ->add('maxPrice', NumberType::class, [
                'required' => false,
                'label' => "Max Price",
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'choice_label' => "name",
                'class' => Category::class,
            ])
            ->add('businessType', EntityType::class, [
                'required' => false,
                'choice_label' => "name",
                'class' => BusinessType::class,
            ])
            ->add('business', EntityType::class, [
                'required' => false,
                'choice_label' => "name",
                'class' => Business::class,
            ])
            ->add('city', SearchType::class,[
                'required' => false,
                'label' => 'City'
            ])
            ->add('available', CheckboxType::class,[
              'required' => false,
              'label' => "Available"
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PackageSearchFilter::class,
            'method' => 'GET',
        ]);
    }
}
