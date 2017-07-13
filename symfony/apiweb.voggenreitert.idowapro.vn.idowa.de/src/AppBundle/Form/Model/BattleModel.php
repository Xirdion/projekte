<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 12:03
 */

namespace AppBundle\Form\Model;


use AppBundle\Entity\Programmer;
use AppBundle\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class BattleModel
{
    /**
     * @var Project
     *
     * @Assert\NotBlank()
     */
    private $project;

    /**
     * @var Programmer
     *
     * @Assert\NotBlank()
     */
    private $programmer;

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return Programmer
     */
    public function getProgrammer()
    {
        return $this->programmer;
    }

    /**
     * @param Programmer $programmer
     */
    public function setProgrammer(Programmer $programmer)
    {
        $this->programmer = $programmer;
    }
}