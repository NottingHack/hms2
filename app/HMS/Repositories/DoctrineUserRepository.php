<?php

namespace HMS\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Entities\Profile;
use HMS\Entities\User;

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

        $profile = new Profile($user);
        $this->em->persist($profile);
        $this->em->flush();

        $user->setProfile($profile);
        $this->em->persist($user);
    }


}
