<?php namespace Arcanys\SSOAuthBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SSOUserProvider implements UserProviderInterface
{
    protected $config;
    protected $doctrine;
    protected $class;

    public function __construct($config,$doctrine){
        $this->config = $config;
        $this->doctrine = $doctrine;
        $this->class = $this->config['user_provider']['class'];
    }

    public function loadUserByUsername($userData)
    {

        $user = $this->doctrine->getRepository($this->class)->findOneByUsername($userData['username']);

        if (!$user) {
            $user = new $this->class();

            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setRoles($userData['roles']);

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
        }
        return $user;

    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        $interfaces = class_implements($class);
        return in_array($this->class,$interfaces);
    }

}
