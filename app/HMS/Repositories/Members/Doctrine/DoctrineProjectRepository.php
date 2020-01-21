<?php

namespace HMS\Repositories\Members\Doctrine;

use HMS\Entities\User;
use HMS\Entities\Members\Project;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Members\ProjectState;
use HMS\Repositories\Members\ProjectRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineProjectRepository extends EntityRepository implements ProjectRepository
{
    use PaginatesFromRequest;

    /**
     * Find by user.
     *
     * @param User $user
     *
     * @return Project[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     * @param bool $active
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page', $active = false)
    {
        $q = parent::createQueryBuilder('project')
            ->where('project.user = :user_id');

        if ($active) {
            $q->andWhere('project.state = :project_state')
              ->orderBy('project.startDate', 'DESC')
              ->setParameter('project_state', ProjectState::ACTIVE);
        }

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Return number of active projects for a user.
     *
     * @param User $user
     *
     * @return int
     */
    public function countActiveByUser(User $user)
    {
        $q = parent::createQueryBuilder('project')
            ->where('project.user = :user_id')
            ->andWhere('project.state = :project_state');

        $q = $q->setParameter('user_id', $user->getId())
                ->setParameter('project_state', ProjectState::ACTIVE)
                ->getQuery();

        return count($q->getResult());
    }

    /**
     * Save Project to the DB.
     *
     * @param Project $project
     */
    public function save(Project $project)
    {
        $this->_em->persist($project);
        $this->_em->flush();
    }
}
