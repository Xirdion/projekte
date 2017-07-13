<?php
/**
 * Created by PhpStorm.
 * User: schmidfl
 * Date: 05.04.2017
 * Time: 16:21
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Asserts;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"username"}, message="It looks like you already have an account!")
 */
class User implements UserInterface
{
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Asserts\NotBlank()
     * @Asserts\Email()
     * @var string $username
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @var string $password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Asserts\NotBlank(groups={"Registration"})
     * A non-persisted field that's used to create the encoded password
     * @var string $plainPassword
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $fName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $lName;


    /**
     * @return integer
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
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        // change password to inform doctrine to update it
        $this->password = null;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $roles
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getSalt()
    {

    }

    public function isAdmin() {
        return $this->role == "ROLE_ADMIN";
    }

    public function getRoles()
    {
        if ($this->role !== "ROLE_USER") {
            return [$this->role, "ROLE_USER"];
        }
        return [$this->role];
    }

    /**
     * @return string
     */
    public function getFName()
    {
        return $this->fName;
    }

    /**
     * @param string $fName
     */
    public function setFName($fName)
    {
        $this->fName = $fName;
    }

    /**
     * @return string
     */
    public function getLName()
    {
        return $this->lName;
    }

    /**
     * @param string $lName
     */
    public function setLName($lName)
    {
        $this->lName = $lName;
    }
}