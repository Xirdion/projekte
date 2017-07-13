<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 15:33
 */

namespace AppBundle\Form;


use AppBundle\Entity\User;
use AppBundle\Form\Model\QuestionModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class QuestionType
 * @package AppBundle\Form
 */
class QuestionType extends AbstractType
{
    /**
     * Define the fields for the form to create a new Question(-Model)
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextType::class)
            ->add('username', EntityType::class, [
                'class'         => User::class,
                'property_path' => 'user',
                'choice_value'  => 'username'
            ]);
    }

    /**
     * Set the QuestionModel as data_class
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => QuestionModel::class,
            'csrf_protection' => false
        ]);
    }

}