<?php
namespace Api\Services;

/**
 * Base Service.
 */
class BaseService {

    // application object
    protected $app;
    
    // database access object
    protected $db;

    // array of deendencies to check before delete
    protected $dependencies;

    // table name
    protected $table_name;
    
    
    /**
     * Constructor.
     */
    public function __construct($app) {
        $this->app = $app;
        $this->db = $this->app["db"];
    }


    /**
     * Check entity dependencies for delete.
     * @return true if entity has dependencies in other tables, false in other case.
     */
    public function hasDependencies($id) {
    	$hasDependencies = false;

    	foreach ($this->dependencies as $table => $dependency) {
    		$foreign_key = $dependency['foreign_key'];

    		$query = "SELECT id FROM ${table} WHERE ${foreign_key} = ${id}";
    		$items = $this->db->fetchAll($query);
    		if (!empty($items)) {
    			$hasDependencies = true;
    			break;
    		}
    	}

    	return $hasDependencies;
    }


    /**
     * @return $entity or false.
     */ 
    public function getByIdSql($id, $sql) {
        $query = $this->db->executeQuery($sql, [$id]);
        $entity = $query->fetch();
        return $entity;
    }


    /**
     * Create entity.
     */
    public function save($entity) {
        $this->db->insert($this->table_name, $entity);
        return $this->db->lastInsertId();
    }
    

    /**
     * Update entity.
     */
    public function update($id, $entity) {
        return $this->db->update($this->table_name, $entity, ['id' => $id]);
    }


    /**
     * Delete entity.
     */
    public function delete($id) {
        return $this->db->delete($this->table_name, ["id" => $id]);
    }


    /**
     * Delete entity from a field value
     */
    public function deleteFromField($field, $value) {
        return $this->db->delete($this->table_name, [$field => $value]);
    }

}