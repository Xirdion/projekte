<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Hateoas\Relation(
 *     "questions",
 *     href=@Hateoas\Route("api_user_questions", parameters={ "username"="expr(object.getUsername())" })
 * )
 */

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 * @UniqueEntity(fields={ "username" }, message="Username '{{ value }}' is already taken")
 * @UniqueEntity(fields={ "email" }, message="There is already account for '{{ value }}'")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "questions",
 *     href=@Hateoas\Route("api_user_questions", parameters={ "username"="expr(object.getUsername())" })
 * )
 *
 */
class User implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", unique=true, nullable=false, options={"comment"="unique username to login"})
     *
     * @Serializer\Expose()
     */
    private $username;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @Serializer\Expose()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $plainpassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPlainpassword()
    {
        return $this->plainpassword;
    }

    /**
     * @param string $plainpassword
     */
    public function setPlainpassword($plainpassword)
    {
        $this->plainpassword = $plainpassword;

        // inform doctrine that the password has changed
        $this->password = null;
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
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     *
     */
    public function getSalt()
    {
        return;
    }

    /**
     *
     */
    public function eraseCredentials()
    {
    }
}
