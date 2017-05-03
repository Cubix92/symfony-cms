<?php

namespace CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'Email'))
            ->add('role', ChoiceType::class, array(
                'label' => 'Rola',
                'choices'  => array(
                    'Redaktor naczelny' => 'ROLE_ADMIN',
                    'Redaktor' => 'ROLE_EDITOR'
                ),
            ))
            ->add('name', TextType::class, array(
                'label' => 'Imię'
            ))
            ->add('surname', TextType::class, array(
                'label' => 'Nazwisko'
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Pola z hasłami powinny do siebie pasować.',
                'options' => array('attr' => array('class' => 'form-control')),
                'required' => true,
                'first_options'  => array('label' => 'Hasło'),
                'second_options' => array('label' => 'Powtórz hasło'),
            ))
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
            'data_class' => 'CmsBundle\Entity\Admin',
            'attr' => array('novalidate' => 'novalidate')
        ));
    }
}
