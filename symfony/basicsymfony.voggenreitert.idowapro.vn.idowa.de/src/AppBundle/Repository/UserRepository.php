<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 11.04.2017
 * Time: 09:40
 */

namespace AppBundle\Repository;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    /**
     * get all Users, who are admins
     * @return array
     */
    public function getAllAdmins() {
        return $this->createQueryBuilder('user')
            ->select('user.id')
            ->andWhere('user.role = :role')
            ->setParameter('role', 'ROLE_ADMIN')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * delete all users, whose id are in the Id-Array
     * @param array $userIds
     */
    public function deleteUsersById(array $userIds) {
        $em = $this->getEntityManager();
        foreach ($userIds as $id) {
            /** @var User $user */
            $user  = $em->getPartialReference('AppBundle:User', $id);
            $em->remove($user);
        }
        $em->flush();
    }

    /**
     * search all users by given string. searching on username, fName and lName
     * @param $search
     * @return User[]
     */
    public function filterUsers($search) {
        return $this->createQueryBuilder('user')
            ->orWhere('user.username LIKE :search')
            ->orWhere('user.fName LIKE :search')
            ->orWhere('user.lName LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $search
     * @param array $sort
     * @return User[]
     */
    public function loadUsers($limit, $offset, $search = null, $sort = null) {
        if ($limit < 1) {
            $limit = 100;
        }
        if ($offset < 0) {
            $offset = 0;
        }
        /**
         * @var QueryBuilder
         */
        $qb = $this->createQueryBuilder('user');
        if ($search) {
            $qb->orWhere('user.username LIKE :search')
                ->orWhere('user.fName LIKE :search')
                ->orWhere('user.lName LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
        if ($sort) {
            if ($sort['col'] == 'users') {
                $qb->orderBy('user.username', $sort['dir']);
            } elseif ($sort['col'] == 'admin') {
                $qb->orderBy('user.role', $sort['dir']);
            }
        }
        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->execute();
    }
}