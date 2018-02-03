<?php
namespace Api\Services;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Users service.
 */
class UsersService extends BaseService {

    // dependencies for delete.
    protected $dependencies = [
        'user_profiles_details' => [
            'foreign_key' => 'user_id'
        ]
    ];

    // entity table name
    protected $table_name = 'users';


    
    /**
     * Get a sorted page of entities.
     *
     * @param $params array
     */
    public function paginatedSearch($params, $app) {

        $join_roles = "(SELECT users_roles.user_id AS user_id, GROUP_CONCAT(roles.name SEPARATOR ',') AS roles FROM roles JOIN users_roles ON users_roles.role_id = roles.id GROUP BY users_roles.user_id)";

        // get items page
        $search_params = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('users.id AS id', 'username', 'email', 'name', 'roles', 'active', 'verified');
        $qb->from($this->table_name);
        $qb->join(
            'users',
            $join_roles, 
            'r',
            'r.user_id = users.id');
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(username LIKE ? OR name LIKE ? OR roles LIKE ?)');
            $search = '%'.$params['search_fields']['search'].'%';
            for ($i = 0; $i < 3; $i++) {
                $search_params[] = $search;
            }
        }
        $qb->orderBy($params['sort_field'], $params['sort_dir']);
        $qb->setFirstResult($params['page_size'] * ($params['page'] - 1));
        $qb->setMaxResults($params['page_size']);

        $items = [];
        $items = $this->db->fetchAll($qb->getSql(), $search_params);    


        // get total count
        $qb = $this->db->createQueryBuilder();
        $qb->select('count(*) AS total');
        $qb->from($this->table_name);
        $qb->join(
            'users',
            $join_roles, 
            'r',
            'r.user_id = users.id');
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(username LIKE ? OR name LIKE ? OR roles LIKE ?)');
        }
        $total = [['total' => 0]];
        $total = $this->db->fetchAll($qb->getSql(), $search_params);
        

        // return result
        return [
            'total' => $total[0]['total'], 
            'items' => $items
        ];
    }
    

    /**
     * Get users by role.
     */
    public function getByRol($roles = []) {
        $join_roles = "(SELECT users_roles.user_id AS user_id, GROUP_CONCAT(roles.name SEPARATOR ',') AS roles FROM roles JOIN users_roles ON users_roles.role_id = roles.id GROUP BY users_roles.user_id)";

        if (empty($roles)) {
            return false;
        } else {
            $likes_values = [];
            $likes = '';
            foreach ($roles as $rol) {
                $likes_values[] = '%'.$rol.'%';
                if (empty($likes)) {
                    $likes .= 'roles LIKE ?';
                } else {
                    $likes .= ' OR roles LIKE ?';
                }
            }

            $qb = $this->db->createQueryBuilder();
            $qb->select('id', 'username', 'email', 'name', 'roles', 'active', 'verified');
            $qb->from('users');
            $qb->join(
                'users',
                $join_roles, 
                'r',
                'r.user_id = users.id');
            $qb->where('active = 1 AND ('.$likes.')');

            $users = $this->db->fetchAll($qb->getSql(), $likes_values);

            return $users;
        }
    }


    /**
     * Get user by id.
     */
    public function getById($id) {
        $join_roles = "(SELECT users_roles.user_id AS user_id, GROUP_CONCAT(roles.name SEPARATOR ',') AS roles FROM roles JOIN users_roles ON users_roles.role_id = roles.id GROUP BY users_roles.user_id)";

        $stmt = $this->db->executeQuery('SELECT users.id AS id, username, name, active, verified, roles, email FROM users JOIN '.$join_roles.' r ON r.user_id = users.id WHERE id = ?', [$id]);

        $user = $stmt->fetch();

        return $user;
    }


    /**
     * Check if username/password is valid.
     */
    /*public function isValidPassword($username, $password) {
        $qb = $this->db->createQueryBuilder();
        $qb->select('id');
        $qb->from('users');
        $qb->where('username = ? AND password = ?');

        $users = $this->db->fetchAll($qb->getSql(), [$username, $password]);

        return !empty($users);
    }*/
    
}