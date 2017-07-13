<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 19.04.2017
 * Time: 14:05
 */

namespace AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProgrammerType extends ProgrammerType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['is_edit' => true]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'programmer_edit';
    }
}