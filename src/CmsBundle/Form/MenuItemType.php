<?php

namespace CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MenuItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttribute('name', 'new_form')
            ->add('parent', EntityType::class, array(
                'class' => 'CmsBundle:MenuItem',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => '- root -',
                'empty_data' => null,
                'label' => 'Rodzic'
            ))
            ->add('name', TextType::class, array('label' => 'Nazwa'))
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'Podstrona' => 'page',
                    'Link' => 'url',
                    'Strona kontaktowa' => 'contact',
                    'Lista aktualności' => 'news'
                ),
                'label' => 'Typ'
            ))
            ->add('inBlank', CheckboxType::class, array('label' => 'Otwierać w nowym oknie'))
            ->add('isVisible', CheckboxType::class, array('label' => 'Pokazywać w menu'))
            ->add('save', SubmitType::class, array('label' => 'Zapisz', 'attr' =>
                array('class' => 'btn btn-primary'))
            );
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CmsBundle\Entity\MenuItem',
            'attr' => array('novalidate' => 'novalidate')
        ));
    }
}
