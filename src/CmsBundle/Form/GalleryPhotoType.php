<?php

namespace CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use CmsBundle\Entity\GalleryPhoto;

class GalleryPhotoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filename', FileType::class, array(
                'label' => 'Wrzuć zdjęcie',
                'data_class' => null
            ))
            ->add('title', TextType::class, array('label' => 'Tytuł'))
            ->add('url', TextType::class, array('label' => 'Link'))
            ->add('description', TextareaType::class, array(
                'label' => 'Opis obrazka',
                'attr' => array('class' => 'form-control', 'rows' => 10)
            ))
            ->add('alt', TextType::class, array('label' => 'Opis alternatywny'))
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
            'data_class' => 'CmsBundle\Entity\GalleryPhoto',
            'attr' => array('novalidate'=>'novalidate')
        ));
    }

}
