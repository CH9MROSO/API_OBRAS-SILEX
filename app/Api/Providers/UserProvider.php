<?php
namespace Api\Providers;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Api\Providers\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\DBAL\Connection;


/**
 * User provider.
 */
class UserProvider implements UserProviderInterface {
    
    private $conn;

    
    /**
     * Constructor.
     */
    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }


    /**
     * 
     */
    public function loadUserByUsername($username) {
        
        $join_roles = "(SELECT users_roles.user_id AS user_id, GROUP_CONCAT(roles.name SEPARATOR ',') AS roles FROM roles JOIN users_roles ON users_roles.role_id = roles.id GROUP BY users_roles.user_id)";

        $stmt = $this->conn->executeQuery('SELECT users.id AS id, username, password, name, active, verified, roles, email FROM users JOIN '.$join_roles.' r ON r.user_id = users.id WHERE username = ?', array(strtolower($username)));

        

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('El usuario "%s" no existe.', $username));
        }
        
        $userObj = new User($user['username'], $user['password'], explode(',', $user['roles']), ($user['active'] == 1), true, true, true);
        $userObj->setActive($user['active']);
        $userObj->setEmail($user['email']);
        $userObj->setName($user['name']);
        $userObj->setId($user['id']);


        return $userObj;
    }

    /**
     * 
     */
    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instancias de "%s" no soportadas.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     *
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
    
}