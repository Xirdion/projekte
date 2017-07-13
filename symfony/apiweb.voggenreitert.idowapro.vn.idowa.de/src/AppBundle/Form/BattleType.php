<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 12:06
 */

namespace AppBundle\Form;


use AppBundle\Form\Model\BattleModel;
use AppBundle\Repository\ProgrammerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BattleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('programmerId', EntityType::class, [
                'class'         => 'AppBundle\Entity\Programmer',
                'property_path' => 'programmer',
                'query_builder' => function(ProgrammerRepository $repository) use ($user) {
                    return $repository->createQueryBuilderForUser($user);
                }
            ])
            ->add('projectId', EntityType::class, [
                'class'         => 'AppBundle\Entity\Project',
                'property_path' => 'project'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => BattleModel::class,
            'csrf_protection' => false
        ]);

        $resolver->setRequired('user');
    }
}