<?php

namespace Main\CatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Main\NuberBundle\Form\SiteUserType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user','Main\NuberBundle\Form\SiteUserType')
            ->add('comment',null,array('required'=>false))
            ->add('isPickUp',ChoiceType::class,array(
                        'choices' => array(
                        'Доставка' => true,
                        'Самовывоз' => false
                    ),
                'choices_as_values' => true,
                'expanded' => true,
            
            'required'=>false));    
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\CatalogBundle\Entity\Order'
        ));
    }
}
