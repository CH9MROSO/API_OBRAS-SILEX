<?php
namespace Api\Services;

/**
 * Obras Service.
 */
class ObrasService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'obra';


    /**
     * Get a sorted page of entities.
     *
     * @param $params array
     */
    public function paginatedSearch($params) {
        // get items page
        $search_params = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('fecha_inicio', 'fecha_fin', 'descripcion', 'ubicacion', 'estado');
        $qb->from($this->table_name);
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(descripcion LIKE ? OR ubicacion LIKE ?)');
            $search = '%'.$params['search_fields']['search'].'%';
            for ($i = 0; $i < 2; $i++) {
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
            $qb->andWhere('(descripcion LIKE ? OR ubicacion LIKE ?)');
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
     * Get all entities.
     *
     * @return $entity or false.
     */ 
    public function getAll() {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table_name);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get entity by id.
     *
     * @return $entity or false.
     */ 
    public function getById($id) {
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->table_name, 'o')
            ->where('o.id = ' . $id);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Save obra.
     */
    public function save($obra) {
        $this->db->insert($this->table_name, $obra);
        return $obra['id'];
    }


    /**
     * Delete obra by user id.
     */
    public function deleteById($id) {
        $this->db->delete($this->table_name, ['id' => $id]);
    }

}
