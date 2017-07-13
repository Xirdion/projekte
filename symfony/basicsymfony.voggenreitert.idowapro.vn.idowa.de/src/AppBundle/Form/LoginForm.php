<?php
/**
 * Created by PhpStorm.
 * User: schmidfl
 * Date: 05.04.2017
 * Time: 16:20
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', EmailType::class, [
                'label' => 'Benutzername:'
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Kennwort:'
            ]);
    }
}