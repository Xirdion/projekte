<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 10.04.2017
 * Time: 08:24
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', EmailType::class, [
                'label' => 'Benutzername'
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Kennwort'
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Benutzergruppe',
                /*
                'choices' => [
                    'Benutzer' => 'ROLE_USER',
                    'Admin'    => 'ROLE_ADMIN'
                ]
                */
                'choices' => $options['roleList'],
                'choice_label' => function($value, $key, $index){
                    /*
                    switch ($value){
                        case "ROLE_USER":
                            return "DAU";
                        case "ROLE_ADMIN":
                            return "Chef";
                        case "ROLE_BACKEND_ACCESS":
                            return "sollte man lieber nicht zuweisen!";
                            default:
                                return $value;
                        }
                    */
                    return $value;
                }
            ])
            ->add('fName', null, [
                'label' => 'Vorname'
            ])
            ->add('lName', null, [
                'label' => 'Nachname'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'roleList' => array()
        ]);
    }
}