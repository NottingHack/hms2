<?php

namespace HMS\Repositories;

use HMS\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class DoctrineUserRepository implements UserRepository
{
    /** @var ObjectRepository */
    private $genericRepository;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * DoctrineUserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->genericRepository = $em->getRepository(User::class);
    }

    /**
     * @param int $id
     * @return User
     */
    public function find(int $id) : User
    {
        return $this->genericRepository->find($id);
    }

    /**
     * @param string $username
     * @return User
     */
    public function findByUsername(string $username) : User
    {
        return $this->genericRepository->findBy(['username' => $username]);
    }

    /**
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email) : User
    {
        return $this->genericRepository->findBy(['email' => $email]);
    }

    /**
     * @param User $user
     */
    public function create(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}
