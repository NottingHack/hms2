<?php

namespace HMS\Auth;

use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Hashing\Hasher;

class DoctrinePasswordStore implements PasswordStore
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * DoctrinePasswordStore constructor.
     *
     * @param UserRepository $userRepository
     * @param Hasher $hasher
     */
    public function __construct(
        UserRepository $userRepository,
        Hasher $hasher
    ) {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * Add a new user with the given username and password.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function add($username, $password)
    {
        $this->setPassword($username, $password);
    }

    /**
     * Remove the user with the given username.
     *
     * @param string $username
     *
     * @return void
     */
    public function remove($username)
    {
        $user = $this->userRepository->findOneByUsername($username);
        $this->setPassword('');
        $this->userRepository->save($user);
    }

    /**
     * Check if a user with the given username exists.
     *
     * @param string $username
     *
     * @return bool
     */
    public function exists($username)
    {
        $user = $this->userRepository->findOneByUsername($username);

        if (is_null($user)) {
            return false;
        }

        return $user->getPassword() != '';
    }

    /**
     * Set the password for the given user.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    public function setPassword($username, $password)
    {
        $user = $this->userRepository->findOneByUsername($username);
        $user->setPassword($this->hasher->make($password));
        $this->userRepository->save($user);
    }

    /**
     * Check the password for the given username.
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($username, $password)
    {
        $user = $this->userRepository->findOneByUsername($username);

        return $this->hasher->check($password, $user->getPassword());
    }
}
