<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 12.04.2017
 * Time: 07:55
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class UserDeleteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('selectedusers', HiddenType::class);
    }
}