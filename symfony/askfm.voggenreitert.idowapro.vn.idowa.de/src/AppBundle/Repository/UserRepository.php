<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Entity\User;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * @param string $filter
     * @param string $sort
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createUserQueryBuilder($filter = '', $sort = '')
    {
        $qb = $this->createQueryBuilder('user');
        if ($filter) {
            $qb->andWhere('user.username LIKE :filter')
                ->orWhere('user.email LIKE :filter')
                ->setParameter('filter','%'.$filter.'%');
        }
        $qb->orderBy('user.createdAt', $sort == 'desc' ? 'DESC' : 'ASC');
        return $qb;
    }

    /**
     * @param string $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->findOneBy(array(
            'username' => $username
        ));
        return $user;
    }

    /**
     * @param string $email
     * @return User
     */
    public function findUserByEmail($email)
    {
        /** @var User $user */
        $user = $this->findOneBy(array(
            'email' => $email
        ));
        return $user;
    }

    /**
     * @param string $username
     * @return User
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsername($username);

        // allow login by email too
        if (!$user) {
            $user = $this->findUserByEmail($username);
        }

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     * @return User|UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'AppBundle\Entity\User';
    }
}
