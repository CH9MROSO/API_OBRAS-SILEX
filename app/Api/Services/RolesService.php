<?php
namespace Api\Services;

/**
 * Roles service.
 */
class RolesService extends BaseService {
    
    /**
     * Get all roles
     */ 
    public function getAll() {
        return $this->db->fetchAll("SELECT id, name, description, application_id FROM roles ORDER BY id ASC");
    }


    /**
     * Get role id from role name.
     */
    public function getId($role_name) {
    	$sql = "SELECT id from roles WHERE name = ?";
    	$query = $this->db->executeQuery($sql, [$role_name]);
        $entity = $query->fetch();

        return $entity['id'];
    }


    /**
     * Save user role.
     */
    public function saveUserRole($user_role) {
    	$this->db->insert("users_roles", $user_role);
    }

    /**
     * Delete user roles.
     */
    public function deleteUserRoles($user_id) {
        $this->db->delete('users_roles', ['user_id' => $user_id]);
    }
    
}