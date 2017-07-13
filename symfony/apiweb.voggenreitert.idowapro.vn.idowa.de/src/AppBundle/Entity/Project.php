<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="battle_project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * 1-10 difficulty level of the project
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $difficultyLevel;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getDifficultyLevel()
    {
        return $this->difficultyLevel;
    }

    /**
     * @param $difficultyLevel
     */
    public function setDifficultyLevel($difficultyLevel)
    {
        $this->difficultyLevel = $difficultyLevel;
    }
}
