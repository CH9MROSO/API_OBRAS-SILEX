<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Visitas Controller.
 */
class VisitasController extends BaseController {


	/**
     * Constructor.
     */
    public function __construct($app, $service, $otherServices) {
        $this->app = $app;
        $this->service = $service;
        $this->otherServices = $otherServices;
        $this->search_fields = [
            'paginatedSearch' => [ // function name
                '*' => ['search'] // rol name or * and an array of search field names. Only fields from frontend.
            ]
        ];
        $this->permissions = [
            'paginatedSearch' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'getAll' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'getAllByIdObra' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'getById' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'save' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'update' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'delete' => ['ROLE_SUPER_ADMIN', 'ROLE_USUARIO']
        ];
    }


    /**
     * Paginated search.
     */
    public function paginatedSearch(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        // build search functions
        $extra_params = [];
        $this->buildSearchParams(__FUNCTION__, $request, $extra_params);


        // paginated search
        $items = $this->service->paginatedSearch($this->search_params);


        // result
        $data = [
            'success' => true,
            'items' => $items['items'],
            'total' => $items['total'],
            'page' => $request->get('page'),
            'page_size' => $request->get('page_size')
        ];
        return $this->app->json($data, Response::HTTP_OK);
    }

    /**
     * Get all visitas.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $visitas = $this->service->getAll();

        if ($visitas){
            $data = [
                'success' => true,
                'visitas' => $visitas
            ];
    
            return $this->app->json($data, Response::HTTP_OK);
        } else {
            $data = [
                'success' => false,
                'error' => 'No se puedo obtener, identificador no encontrado.'
            ];
            return $this->app->json($data, Response::HTTP_OK);
        }

    }

    /**
     * Get all visitas by Id Obra.
     *
     */ 
    public function getAllByIdObra($idObra) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $visitas = $this->service->getAllByIdObra($idObra);
        

        if ($visitas){
            $data = [
                'success' => true,
                'visitas' => $visitas
            ];
    
            return $this->app->json($data, Response::HTTP_OK);
        } else {
            $data = [
                'success' => false,
                'error' => 'No se puedo obtener, identificador no encontrado.'
            ];
            return $this->app->json($data, Response::HTTP_OK);
        }

    }

    /**
     * Get visita by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $visita = $this->service->getById($id);
        
/*         $user = $this->getCurrentUser(); */

        if ($visita){
            $data = [
                'success' => true,
                'visita' => $visita
            ];
    
            return $this->app->json($data, Response::HTTP_OK);
        } else {
            $data = [
                'success' => false,
                'error' => 'No se puedo obtener, identificador no encontrado.'
            ];
            return $this->app->json($data, Response::HTTP_OK);
        }

    }

    /**
     * Save visita.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $data = $this->getDataFromRequest($request);

        // save visita
        $visita = $data['visita'];

        $id = $this->service->save($visita);

        return $this->app->json([
            'success' => true,
            'id' => $id
            ], 
            Response::HTTP_OK
        );
    }


    /**
     * Update entity.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // entity data
        $data = $this->getDataFromRequest($request);

        // update visita
        $visita = $data['visita'];

        // update entity
        $updated_visita = [];
        if (isset($visita['obra_id'])) {
            $updated_visita['obra_id'] = $visita['obra_id'];
        }
        if (isset($visita['num_visita'])) {
        	$updated_visita['num_visita'] = $visita['num_visita'];
        }
        if (isset($visita['fecha'])) {
        	$updated_visita['fecha'] = $visita['fecha'];
        }
        if (isset($visita['fase'])) {
        	$updated_visita['fase'] = $visita['fase'];
        }
        if (isset($visita['observaciones'])) {
        	$updated_visita['observaciones'] = $visita['observaciones'];
        }
        if (isset($visita['elementos'])) {
        	$updated_visita['elementos'] = $visita['elementos'];
        }
        if (isset($visita['estado_elementos'])) {
        	$updated_visita['estado_elementos'] = $visita['estado_elementos'];
        }
        if (isset($visita['documentos'])) {
        	$updated_visita['documentos'] = $visita['documentos'];
        }
        if (isset($visita['estado_documentos'])) {
        	$updated_visita['estado_documentos'] = $visita['estado_documentos'];
        }
        if (isset($visita['profilePic'])) {
            $updated_visita['profilePic'] = $visita['profilePic'];
        }
       
        $this->service->update($id, $updated_visita);

		return new JsonResponse(['success' => true]);
    }


    /**
     * Delete entity.
     */
    public function delete($id) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        // check dependencies
        if ($this->service->hasDependencies($id)) {
            $response = [
                'success' => false,
                'error' => 'No se puede eliminar porque hay otros datos relacionados con él.'
            ];             
            return $this->app->json($response, Response::HTTP_OK);
        } else {
            $visita = $this->service->getById($id);
            if ($visita) {
                $this->service->delete($id);
                return new JsonResponse(['success' => true]);
            }else {
                $response = [
                    'success' => false,
                    'error' => 'No se puede eliminar, identificador no encontrado.'
                ];             
                return $this->app->json($response, Response::HTTP_OK);
            }
        }
    }

}
