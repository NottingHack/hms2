<?php

namespace HMS\Repositories\Forms\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\User;
use HMS\Repositories\Forms\FormResponseRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineFormResponseRepository extends EntityRepository implements FormResponseRepository
{
    use PaginatesFromRequest;

    /**
     * Paginates all forms.
     *
     * @param Form $form
     * @param bool $includeArchived = false
     * @param int $perPage = 15
     * @param string $pageName = 'page'
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateForForm(Form $form, $includeArchived = false, $perPage = 15, $pageName = 'page')
    {
        $queryBuilder = $this->createQueryBuilder('responses');

        $hiddenQuery = '';
        if (! $includeArchived) {
            $hiddenQuery .= ' and responses.hidden = false';
        }

        $queryBuilder->where('responses.form = :form_id' . $hiddenQuery);
        $queryBuilder->setParameter('form_id', $form->getId());

        $queryBuilder->orderBy('responses.createdAt', 'DESC');
        $query = $queryBuilder->getQuery();

        return $this->paginate($query, $perPage, $pageName);
    }

    /**
     * Count the number of responses to a given form by a user.
     *
     * @param Form $form
     * @param User $user
     *
     * @return int
     */
    public function countUserResponsesForForm(Form $form, User $user)
    {
        $qb = parent::createQueryBuilder('responses')
            ->select('COUNT(responses.id)')
            ->where('responses.responder = :user')
            ->andWhere('responses.form = :form');

        $qb->setParameter('user', $user);
        $qb->setParameter('form', $form);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Count the number of visible responses to a form.
     *
     * @param Form $form
     *
     * @return int
     */
    public function countVisibleResponsesForForm(Form $form)
    {
        $qb = parent::createQueryBuilder('responses')
            ->select('COUNT(responses.id)')
            ->where('responses.form = :form')
            ->andWhere('responses.hidden = false');

        $qb->setParameter('form', $form);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Commit the form response to the database.
     *
     * @param FormResponse $formResponse
     *
     * @return void
     */
    public function save(FormResponse $formResponse)
    {
        $this->_em->persist($formResponse);
        $this->_em->flush();
    }
}
