<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 27.04.2017
 * Time: 08:57
 */

namespace AppBundle\Repository;


use AppBundle\Entity\User;
use AppBundle\Question\QuestionStatus;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository
{
    /**
     * Create QueryBuilder to find all Questions for this user with a special status and maybe a filter
     *
     * @param User $user
     * @param string $status
     * @param string $sort
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQuestionQueryBuilder(User $user, string $status, $sort = '')
    {
        $qb = $this->createQueryBuilder('q')
            ->andWhere('q.user = :user')
            ->setParameter('user', $user->getId());

        if (!$status) {
            $status = QuestionStatus::Unanswered;
        }
        if ($status != QuestionStatus::All) {
            $qb->andWhere('q.status LIKE :status')
                ->setParameter('status', $status);
        }
        $qb->orderBy('q.createdAt', $sort=='desc' ? 'DESC' : 'ASC');

        return $qb;
    }
}