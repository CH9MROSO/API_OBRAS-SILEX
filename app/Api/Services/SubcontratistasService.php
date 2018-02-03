<?php
namespace Api\Services;

/**
 * Subcontratistas Service.
 */
class SubcontratistasService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'subcontratista';


    /**
     * Get a sorted page of entities.
     *
     * @param $params array
     */
    public function paginatedSearch($params) {
        // get items page
        $search_params = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('contacto_id', 'obra_id', 'contructor_id', 'intervencion');
        $qb->from($this->table_name);
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(contacto_id LIKE ? OR obra_id LIKE ? OR constructor_id LIKE ? OR intervencion LIKE ?)');
            $search = '%'.$params['search_fields']['search'].'%';
            for ($i = 0; $i < 4; $i++) {
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
            $qb->andWhere('(contacto_id LIKE ? OR obra_id LIKE ? OR constructor_id LIKE ? OR intervencion LIKE ?)');
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
     * Get all subcontratistas.
     *
     * @return $subcontratistas or false.
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
     * Get subcontratista by id.
     *
     * @return $subcontratista or false.
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
                'a.obra_id', 'a.constructor_id', 'a.intervencion')
            ->from($this->table_name, 'a')
            ->join('a', 'contacto', 'c', 'a.contacto_id = c.id')
            ->where('a.id = ' . $id);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get subcontratistas by id Obra.
     *
     * @return $subcontratistas or false.
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
                'a.constructor_id', 'a.intervencion')
            ->from($this->table_name, 'a')
            ->join('a', 'contacto', 'c', 'a.contacto_id = c.id')
            ->where('a.obra_id = ' . $idObra);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Save subcontratista.
     */
    public function save($subcontratista) {
        $this->db->insert($this->table_name, $subcontratista);
        return $this->db->lastInsertId();
    }


    /**
     * Delete subcontratista by user id.
     */
    public function deleteById($id) {
        $this->db->delete($this->table_name, ['id' => $id]);
    }

}
