<?php
namespace Api\Services;

/**
 * UserProfiles Service.
 */
class UserProfilesService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'user_profiles_details';


    /**
     * Get a sorted page of entities.
     *
     * @param $params array
     */
    public function paginatedSearch($params) {
        // get items page
        $search_params = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('id', 'user_id', 'first_name', 'surname', 'birthday', 'gender', 'appointment', 'collage', 'num_collage', 'email');
        $qb->from($this->table_name);
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(first_name LIKE ? OR surname LIKE ? OR appointment LIKE ? gender LIKE ? OR collage LIKE ? OR num_collage LIKE ? OR email LIKE ?)');
            $search = '%'.$params['search_fields']['search'].'%';
            for ($i = 0; $i < 7; $i++) {
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
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(first_name LIKE ? OR surname LIKE ? OR appointment LIKE ? gender LIKE ? OR collage LIKE ? OR num_collage LIKE ? OR email LIKE ?)');
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
     * Get entity by id.
     *
     * @return $entity or false.
     */ 
    public function getById($id) {
        $sql = "SELECT id, user_id, first_name, surname, birthday, gender, appointment, collage, num_collage, email, role FROM ".($this->table_name)." WHERE id = ?";
        return parent::getByIdSql($id, $sql);
    }


    /**
     * Get entity by user id.
     *
     * @return $entity or false.
     */ 
    public function getByUser($user_id) {
         $sql = "SELECT id, user_id, first_name, surname, birthday, gender, appointment, collage, num_collage, email, role FROM ".($this->table_name)." WHERE user_id = ?";
        return parent::getByIdSql($user_id, $sql);
    }


    /**
     * Delete user profile by user id.
     */
    public function deleteByUserId($user_id) {
        $this->db->delete($this->table_name, ['user_id' => $user_id]);
    }

}
