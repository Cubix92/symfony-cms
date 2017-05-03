<?php

namespace CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SettingsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('general_title', TextType::class, array('label' => 'Tytuł strony'))
            ->add('general_phone', TextType::class, array('label' => 'Telefon kontaktowy', 'required' => false))
            ->add('general_email', EmailType::class, array('label' => 'Email kontaktowy', 'required' => false))
            ->add('general_worktime', TextType::class, array('label' => 'Godziny pracy', 'required' => false))
            ->add('general_address', TextType::class, array('label' => 'Adres', 'required' => false))
            ->add('general_logo', FileType::class, array(
                'label' => 'Logo', 'required' => false, 'data_class' => null
            ))
            ->add('google_analytics', TextareaType::class, array('label' => 'Kod śledzący', 'attr' => array('rows' => 8)))
            ->add('client_secrets', FileType::class, array(
                'label' => 'Klucz do GA', 'required' => false, 'data_class' => null
            ))
            ->add('main_banner_1', FileType::class, array(
                'label' => 'Baner 1', 'required' => false, 'data_class' => null
            ))
            ->add('main_banner_2', FileType::class, array(
                'label' => 'Baner 2', 'required' => false, 'data_class' => null
            ))
            ->add('main_banner_3', FileType::class, array(
                'label' => 'Baner 3', 'required' => false, 'data_class' => null
            ))
            ->add('main_banner_4', FileType::class, array(
                'label' => 'Baner 4', 'required' => false, 'data_class' => null
            ))
            ->add('main_banner_5', FileType::class, array(
                'label' => 'Baner 5', 'required' => false, 'data_class' => null
            ))
            ->add('footer_left_box_title', TextType::class, array('label' => 'Tytuł lewego boxa'))
            ->add('footer_left_box_content', TextareaType::class, array('label' => 'Treść lewego boxa'))
            ->add('footer_right_box_title', TextType::class, array('label' => 'Tytuł prawego boxa'))
            ->add('footer_right_box_content', TextareaType::class, array('label' => 'Treść prawego boxa'))
            ->add('social_twitter', TextType::class, array('label' => 'Twitter', 'required' => false))
            ->add('social_facebook', TextType::class, array('label' => 'Facebook', 'required' => false))
            ->add('social_linkedin', TextType::class, array('label' => 'Linkedin', 'required' => false))
            ->add('social_google', TextType::class, array('label' => 'Google+', 'required' => false))
            ->add('social_instagram', TextType::class, array('label' => 'Instagram', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Zapisz konfigurację', 'attr' =>
                array('class' => 'btn btn-success btn-xs pull-right btn-config', 'form' => 'configuration')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array('id' => 'configuration')
        ));
    }
}
