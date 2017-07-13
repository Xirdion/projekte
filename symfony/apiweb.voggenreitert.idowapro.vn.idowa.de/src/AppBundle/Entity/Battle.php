<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Table(name="battle_battle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattleRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "programmer",
 *     href=@Hateoas\Route(
 *          "api_programmers_show",
 *          parameters={"nickname"="expr(object.getProgrammerNickname())"}
 *     )
 * )
 */
class Battle
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var Programmer
     *
     * @ORM\ManyToOne(targetEntity="Programmer")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Serializer\Expose()
     */
    private $programmer;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Serializer\Expose()
     */
    private $didProgrammerWin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Expose()
     */
    private $foughtAt;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Serializer\Expose()
     */
    private $notes;

    /**
     * Battle constructor.
     * @param Programmer $programmer
     * @param Project $project
     */
    public function __construct(Programmer $programmer, Project $project)
    {
        $this->programmer = $programmer;
        $this->project    = $project;
        $this->foughtAt   = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $notes
     */
    public function setBattleWonByProgrammer($notes)
    {
        $this->didProgrammerWin = true;
        $this->notes = $notes;
    }

    /**
     * @param $notes
     */
    public function setBattleLostByProgrammer($notes)
    {
        $this->didProgrammerWin = false;
        $this->notes = $notes;
    }

    /**
     * @return Programmer
     */
    public function getProgrammer()
    {
        return $this->programmer;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return bool
     */
    public function getDidProgrammerWin()
    {
        return $this->didProgrammerWin;
    }

    /**
     * @return \DateTime
     */
    public function getFoughtAt()
    {
        return $this->foughtAt;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getProgrammerNickname() {
        return $this->programmer->getNickname();
    }

    /**
     * @return int
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("project")
     */
    public function getProjectId() {
        return $this->project->getId();
    }
}
