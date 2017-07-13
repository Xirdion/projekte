<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 16:01
 */

namespace AppBundle\Form\Model;


use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $question;

    /**
     * @var User
     *
     * @Assert\NotBlank()
     */
    private $user;

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
    public function setQuestion(string $question)
    {
        $this->question = $question;
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
    public function setUser(User $user)
    {
        $this->user = $user;
    }


}