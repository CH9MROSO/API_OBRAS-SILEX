<?php
namespace Api\Providers;

use Symfony\Component\Security\Core\User\UserInterface;


/**
 * User.
 */
class User implements UserInterface {
    
    private $id;
    private $username;
    private $password;
    private $enabled;
    private $accountNonExpired;
    private $credentialsNonExpired;
    private $accountNonLocked;
    private $roles;
    private $email;
    private $name;
    private $active;


    /**
     * Constructor.
     */
    public function __construct($username, $password, array $roles = array(), $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        if ('' === $username || null === $username) {
            throw new \InvalidArgumentException('El nombre de usuario no puede ser vacío.');
        }

        $this->username = $username;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
        $this->roles = $roles;
        
        $this->email = null;
        $this->name = null;
        $this->active = $enabled;
    }


    /**
     *
     */
    public function __toString()
    {
        return $this->getUsername();
    }


    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
    
    
    public function getEmail() {
        return $this->email;
    }
    public function getName() {
        return $this->name;
    }
    public function isActive() {
        return $this->active;
    }
    
    public function setEmail($email = null) {
        $this->email = $email;
    }
    public function setName($name = null) {
        $this->name = $name;
    }
    public function setActive($active = false) {
        $this->active = $active;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }
    
}
