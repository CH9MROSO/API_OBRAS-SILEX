<?php
namespace Api\Services;

/**
 * Promotores Service.
 */
class PromotoresService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'promotor';


    /**
     * Get a sorted page of entities.
     *
     * @param $params array
     */
    public function paginatedSearch($params) {
        // get items page
        $search_params = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('contacto_id', 'obra_id', 'intervencion');
        $qb->from($this->table_name);
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(contacto_id LIKE ? OR obra_id LIKE ? OR intervencion LIKE ?)');
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
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(contacto_id LIKE ? OR obra_id LIKE ? OR intervencion LIKE ?)');
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
     * Get all promotores.
     *
     * @return $promotores or false.
     */ 
    public function getAll() {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select(
                'DISTINCT a.contacto_id',
                'c.dni_cif', 'c.nombre_razon', 'c.apellidos', 
                'c.direccion', 'c.cp', 'c.municipio', 'c.Provincia', 
                'c.Pais', 'c.email', 'c.telefono', 'c.tipo_persona_juridica', 'c.representante', 'c.profilePic')
            ->from('contacto', 'c')
            ->join('c', $this->table_name, 'a', 'a.contacto_id = c.id');
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get promotor by id.
     *
     * @return $promotor or false.
     */ 
    public function getById($id) {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select(
                'a.id',
                'a.contacto_id',
                'c.dni_cif', 'c.nombre_razon', 'c.apellidos', 
                'c.direccion', 'c.cp', 'c.municipio', 'c.Provincia', 
                'c.Pais', 'c.email', 'c.telefono', 'c.tipo_persona_juridica', 'c.representante', 'c.profilePic',
                'a.obra_id', 'a.intervencion')
            ->from($this->table_name, 'a')
            ->join('a', 'contacto', 'c', 'a.contacto_id = c.id')
            ->where('a.id = ' . $id);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get promotores by id Obra.
     *
     * @return $promotores or false.
     */ 
    public function getAllByIdObra($idObra) {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select(
                'a.id',
                'a.contacto_id',
                'c.dni_cif', 'c.nombre_razon', 'c.apellidos', 
                'c.direccion', 'c.cp', 'c.municipio', 'c.Provincia', 
                'c.Pais', 'c.email', 'c.telefono', 'c.tipo_persona_juridica', 'c.representante', 'c.profilePic',
                'a.intervencion')
            ->from($this->table_name, 'a')
            ->join('a', 'contacto', 'c', 'a.contacto_id = c.id')
            ->where('a.obra_id = ' . $idObra);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Save Promotor.
     */
    public function save($promotor) {
        $this->db->insert($this->table_name, $promotor);
        return $this->db->lastInsertId();
    }


    /**
     * Delete promotor by user id.
     */
    public function deleteById($id) {
        $this->db->delete($this->table_name, ['id' => $id]);
    }

}
