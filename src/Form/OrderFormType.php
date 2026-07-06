<?php

namespace App\Form;

use App\Entity\Business;
use App\Entity\BusinessType;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Package;
use App\Repository\OrderRepository;
use App\Repository\PackageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('createdAt', DateTimeType::class)
            ->add('package', EntityType::class, [
                'class' => Package::class,
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }


}
