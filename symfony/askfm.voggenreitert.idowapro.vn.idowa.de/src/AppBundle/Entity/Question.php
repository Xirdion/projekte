<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 08:56
 */

namespace AppBundle\Entity;

use AppBundle\Question\QuestionStatus;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class Question
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "author",
 *     href=@Hateoas\Route("api_user_show", parameters={ "username"="expr(object.getAuthorName())" })
 * )
 *
 * @Hateoas\Relation(
 *     "user",
 *     href=@Hateoas\Route("api_user_show", parameters={ "username"="expr(object.getUserName())" })
 * )
 */
class Question
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid", nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", nullable=false)
     *
     * @Serializer\Expose()
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $answer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false, options={"default"="unanswered"})
     *
     * @Serializer\Expose()
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="answered_at", type="datetime", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $answeredAt;

    /**
     * Question constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = QuestionStatus::Unanswered;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("author")
     */
    public function getAuthorName()
    {
        return $this->author->getUsername();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("user")
     */
    public function getUserName()
    {
        return $this->user->getUsername();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getAnsweredAt()
    {
        return $this->answeredAt;
    }

    /**
     * @param \DateTime $answeredAt
     */
    public function setAnsweredAt($answeredAt)
    {
        $this->answeredAt = $answeredAt;
    }

}