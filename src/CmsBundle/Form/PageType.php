<?php

namespace CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'Tytuł'))
            ->add('slug', TextType::class, array(
                'label' => 'Slug',
                'attr' => array('placeholder' => 'Nazwa strony wyświetlana w pasku adresu.')
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'Treść'
            ))
            ->add('meta_title', TextType::class, array(
                'label' => 'Tytuł meta',
                'attr' => array('placeholder' => 'Tytuł strony wykorzystywany do pozycjonowania.')
            ))
            ->add('meta_description', TextareaType::class, array(
                'label' => 'Opis meta',
                'attr' => array(
                    'class' => 'form-control',
                    'rows' => 10,
                    'placeholder' => 'Opis wykorzystywany do pozycjonowania.'
                )
            ))
            ->add('meta_keywords', TextType::class, array(
                'label' => 'Słowa kluczowe',
                'attr' => array('placeholder' => 'Słowa kluczowe wykorzystywane do pozycjonowania')
            ))
            ->add('gallery', EntityType::class, array(
                'class' => 'CmsBundle:Gallery',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => '- brak -',
                'empty_data' => null,
                'label' => 'Galeria'
            ))
            ->add('pageTemplate', EntityType::class, array(
                'class' => 'CmsBundle:PageTemplate',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Szablon'
            ))
            ->add('is_active', CheckboxType::class, array('label' => 'Aktywna', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Zapisz', 'attr' =>
                    array('class' => 'btn btn-success pull-right'))
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CmsBundle\Entity\Page',
            'attr' => array('novalidate' => 'novalidate')
        ));
    }
}
