<?php

namespace HMS\Repositories\Members\Doctrine;

use HMS\Entities\User;
use HMS\Entities\Members\Project;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Members\ProjectRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineProjectRepository extends EntityRepository implements ProjectRepository
{
    use PaginatesFromRequest;

    /**
     * @param User   $user
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('project')
            ->where('project.user = :user_id');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * save Project to the DB.
     * @param  Project $project
     */
    public function save(Project $project)
    {
        $this->_em->persist($project);
        $this->_em->flush();
    }
}
