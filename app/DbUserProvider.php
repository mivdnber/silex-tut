<?php
namespace geography\webapp;

use \Symfony\Component\Security\Core\User\UserProviderInterface;
use \Symfony\Component\Security\Core\User\UserInterface;
use \Symfony\Component\Security\Core\User\User;
use \Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use geography\db as db;

class DbUserProvider implements UserProviderInterface {
    private $db;

    public function __construct(db\Connection $db) {
        $this->db = $db;
    }

    public function loadUserByUsername($username) {
        try {
            $user = $this->db->one('user', ['username' => $username]);
        } catch(db\NotFound $ex) {
            $user = $this->db->go('create_user', [
                'username' => $username,
                'password' => null,
                'email' => 'test',
                'role' => 'ROLE_USER',
                'firstname' => 'Testy',
                'lastname' => 'McTest'
            ])[0];
        }

        return new User(
            $user->username,
            $user->password,
            explode(',', $user->role)
        );
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

}
