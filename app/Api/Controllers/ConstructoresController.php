<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Constructores Controller.
 */
class ConstructoresController extends BaseController {


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
     * Get all constructores.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $constructores = $this->service->getAll();

        if ($constructores){
            $data = [
                'success' => true,
                'constructores' => $constructores
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
     * Get all constructores by Id Obra.
     *
     */ 
    public function getAllByIdObra($idObra) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $constructores = $this->service->getAllByIdObra($idObra);
        

        if ($constructores){
            $data = [
                'success' => true,
                'constructores' => $constructores
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
     * Get constructor by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $constructor = $this->service->getById($id);
        
/*         $user = $this->getCurrentUser(); */

        if ($constructor){
            $data = [
                'success' => true,
                'constructor' => $constructor
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
     * Save constructor.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $data = $this->getDataFromRequest($request);

        // save constructor
        $constructor = $data['constructor'];

        $id = $this->service->save($constructor);

        return $this->app->json([
            'success' => true,
            'id' => $id
            ], 
            Response::HTTP_OK
        );
    }


    /**
     * Update constructor.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // constructor data
        $data = $this->getDataFromRequest($request);

        // update constructor
        $constructor = $data['constructor'];

        // update constructor
        $updated_constructor = [];
        if (isset($constructor['contacto_id'])) {
        	$updated_constructor['contacto_id'] = $constructor['contacto_id'];
        }
        if (isset($constructor['obra_id'])) {
            $updated_constructor['obra_id'] = $constructor['obra_id'];
        }
        if (isset($tecnico['fase_obra'])) {
            $updated_tecnico['fase_obra'] = $tecnico['fase_obra'];
        }
        if (isset($constructor['intervencion'])) {
        	$updated_constructor['intervencion'] = $constructor['intervencion'];
        }
       
        $this->service->update($id, $updated_constructor);

		return new JsonResponse(['success' => true]);
    }


    /**
     * Delete constructor.
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
            $constructor = $this->service->getById($id);
            if ($constructor) {
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
