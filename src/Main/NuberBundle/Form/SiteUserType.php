<?php

namespace Main\NuberBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Main\NuberBundle\Entity\User;

use Main\CatalogBundle\Entity\Order;

use Main\CatalogBundle\Form\OrderType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SiteUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('phone',null,array('required'=>true))
            ->add('street',null,array('required'=>false))
            ->add('house',null,array('required'=>false))
            ->add('build',null,array('required'=>false))
            ->add('apartment',null,array('required'=>false))
           
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\NuberBundle\Entity\User'
        ));
    }
}
