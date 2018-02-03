<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Subcontratistas Controller.
 */
class SubcontratistasController extends BaseController {


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
     * Get all subcontratistas.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $subcontratistas = $this->service->getAll();

        if ($subcontratistas){
            $data = [
                'success' => true,
                'subcontratistas' => $subcontratistas
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
     * Get all subcontratistas by Id Obra.
     *
     */ 
    public function getAllByIdObra($idObra) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $subcontratistas = $this->service->getAllByIdObra($idObra);
        

        if ($subcontratistas){
            $data = [
                'success' => true,
                'subcontratistas' => $subcontratistas
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
     * Get subcontratista by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $subcontratista = $this->service->getById($id);
        
/*         $user = $this->getCurrentUser(); */

        if ($subcontratista){
            $data = [
                'success' => true,
                'subcontratista' => $subcontratista
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
     * Save subcontratista.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $data = $this->getDataFromRequest($request);

        // save subcontratista
        $subcontratista = $data['subcontratista'];

        $id = $this->service->save($subcontratista);

        return $this->app->json([
            'success' => true,
            'id' => $id
            ], 
            Response::HTTP_OK
        );
    }


    /**
     * Update subcontratista.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // subcontratista data
        $data = $this->getDataFromRequest($request);

        // update subcontratista
        $subcontratista = $data['subcontratista'];

        // update subcontratista
        $updated_subcontratista = [];
        if (isset($subcontratista['contacto_id'])) {
        	$updated_subcontratista['contacto_id'] = $subcontratista['contacto_id'];
        }
        if (isset($subcontratista['obra_id'])) {
            $updated_subcontratista['obra_id'] = $subcontratista['obra_id'];
        }
        if (isset($subcontratista['constructor_id'])) {
            $updated_subcontratista['constructor_id'] = $subcontratista['constructor_id'];
        }
        if (isset($subcontratista['intervencion'])) {
        	$updated_subcontratista['intervencion'] = $subcontratista['intervencion'];
        }
       
        $this->service->update($id, $updated_subcontratista);

		return new JsonResponse(['success' => true]);
    }


    /**
     * Delete subcontratista.
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
            $subcontratista = $this->service->getById($id);
            if ($subcontratista) {
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
