<?php
namespace Api\Services;

/**
 * Visitas Service.
 */
class VisitasService extends BaseService {

	// dependencies for delete.
    protected $dependencies = [];

    // entity table name
    protected $table_name = 'visita';


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
     * Get all visitas.
     *
     * @return $visitas or false.
     */ 
    public function getAll() {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select(
                'v.id',
                'v.obra_id',
                'v.num_visita', 'v.fecha', 'v.fase', 
                'v.observaciones', 'v.elementos', 'v.estado_elementos', 
                'v.documentos', 'v.estado_documentos')
            ->from($this->table_name, 'v');
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get visita by id.
     *
     * @return $visita or false.
     */ 
    public function getById($id) {
        // get item
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->table_name, 'v')
            ->where('v.id = ' . $id);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Get visitas by id Obra.
     *
     * @return $visitas or false.
     */ 
    public function getAllByIdObra($idObra) {
        // get items
        $qb = $this->db->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->table_name, 'v')
            ->where('v.obra_id = ' . $idObra);
        $items = [];
        $items = $this->db->fetchAll($qb->getSql());
        // return result
        return $items;
    }

    /**
     * Save visita.
     */
    public function save($visita) {
        $this->db->insert($this->table_name, $visita);
        return $this->db->lastInsertId();
    }


    /**
     * Delete visita by user id.
     */
    public function deleteById($id) {
        $this->db->delete($this->table_name, ['id' => $id]);
    }

}
