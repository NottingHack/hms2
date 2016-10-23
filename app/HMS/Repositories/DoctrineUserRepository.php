<?php

namespace HMS\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Entities\User;

class DoctrineUserRepository implements UserRepository
{
    /** @var ObjectRepository */
    private $genericRepository;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->genericRepository = $em->getRepository(User::class);
    }

    public function find($id)
    {
        return $this->genericRepository->find($id);
    }

    public function findByUsername($username)
    {
        return $this->genericRepository->findBy(['username' => $username]);
    }

    public function findByEmail($email)
    {
        return $this->genericRepository->findBy(['email' => $email]);
    }

    public function create($user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }


}