<?php

namespace CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use CmsBundle\Entity\GalleryPhoto;

class NewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'Tytuł'))
            ->add('content', TextareaType::class, array(
                'label' => 'Treść',
                'attr' => array('class' => 'form-control', 'rows' => 10)
            ))
            ->add('is_published', CheckboxType::class, array('label' => 'Opublikowany', 'required' => false))
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
            'data_class' => 'CmsBundle\Entity\News',
            'attr' => array('novalidate'=>'novalidate')
        ));
    }

}
