<?php
namespace Api\Services;

/**
 * Contactos Service.
 */
class ContactosService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'contacto';


    /**
     * Get a sorted page of entities.
     *
     * @param $params array
     */
    public function paginatedSearch($params) {
        // get items page
        $search_params = [];
        $qb = $this->db->createQueryBuilder();
        $qb->select('dni_cif', 'nombre_razon', 'apellidos', 'direccion', 'cp', 'municipio', 'Provincia', 'Pais', 'email', 'telefono', 'tipo_persona_juridica', 'representante');
        $qb->from($this->table_name);
        if (!empty($params['search_fields']['search'])) {
            $qb->andWhere('(dni_cif LIKE ? OR nombre_razon LIKE ? OR apellidos LIKE ?)');
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
            $qb->andWhere('(dni_cif LIKE ? OR nombre_razon LIKE ? OR apellidos LIKE ?)');
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
     * Get all contactos.
     *
     * @return $contactos or false.
     */ 
    public function getAll() {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->table_name);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get contacto by id.
     *
     * @return $contacto or false.
     */ 
    public function getById($id) {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->table_name, 'c')
            ->where('c.id = ' . $id);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Save contacto.
     */
    public function save($contacto) {
        $this->db->insert($this->table_name, $contacto);
        return $this->db->lastInsertId();
    }


    /**
     * Delete contacto by user id.
     */
    public function deleteById($id) {
        $this->db->delete($this->table_name, ['id' => $id]);
    }

}
