<?php namespace Arcanys\SSOAuthBundle\Security\User;

interface SSOUserInterface{
    public function setUsername($username);
    public function setRoles($roles);
    public function setEmail($email);
    public function setFirstname($firstname);
    public function setLastname($lastname);
}
