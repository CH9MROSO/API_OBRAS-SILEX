<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Tecnicos Controller.
 */
class TecnicosController extends BaseController {


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
     * Get all tecnicos.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $tecnicos = $this->service->getAll();

        if ($tecnicos){
            $data = [
                'success' => true,
                'tecnicos' => $tecnicos
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
     * Get all tecnicos by Id Obra.
     *
     */ 
    public function getAllByIdObra($idObra) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $tecnicos = $this->service->getAllByIdObra($idObra);
        

        if ($tecnicos){
            $data = [
                'success' => true,
                'tecnicos' => $tecnicos
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
     * Get tecnico by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $tecnico = $this->service->getById($id);
        
/*         $user = $this->getCurrentUser(); */

        if ($tecnico){
            $data = [
                'success' => true,
                'tecnico' => $tecnico
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
     * Save tecnico.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $data = $this->getDataFromRequest($request);

        // save tecnico
        $tecnico = $data['tecnico'];

        $id = $this->service->save($tecnico);

        return $this->app->json([
            'success' => true,
            'id' => $id
            ], 
            Response::HTTP_OK
        );
    }


    /**
     * Update tecnico.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // tecnico data
        $data = $this->getDataFromRequest($request);

        // update tecnico
        $tecnico = $data['tecnico'];

        // update tecnico
        $updated_tecnico = [];
        if (isset($tecnico['contacto_id'])) {
        	$updated_tecnico['contacto_id'] = $tecnico['contacto_id'];
        }
        if (isset($tecnico['obra_id'])) {
            $updated_tecnico['obra_id'] = $tecnico['obra_id'];
        }
        if (isset($tecnico['cargo'])) {
            $updated_tecnico['cargo'] = $tecnico['cargo'];
        }
        if (isset($tecnico['intervencion'])) {
        	$updated_tecnico['intervencion'] = $tecnico['intervencion'];
        }
       
        $this->service->update($id, $updated_tecnico);

		return new JsonResponse(['success' => true]);
    }


    /**
     * Delete tecnico.
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
            $Tecnico = $this->service->getById($id);
            if ($Tecnico) {
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
