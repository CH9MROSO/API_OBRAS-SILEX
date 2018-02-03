<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Promotores Controller.
 */
class PromotoresController extends BaseController {


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
     * Get all promotores.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $promotores = $this->service->getAll();

        if ($promotores){
            $data = [
                'success' => true,
                'promotores' => $promotores
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
     * Get all promotores by Id Obra.
     *
     */ 
    public function getAllByIdObra($idObra) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $promotores = $this->service->getAllByIdObra($idObra);
        

        if ($promotores){
            $data = [
                'success' => true,
                'promotores' => $promotores
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
     * Get promotor by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $promotor = $this->service->getById($id);
        
/*         $user = $this->getCurrentUser(); */

        if ($promotor){
            $data = [
                'success' => true,
                'promotor' => $promotor
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
     * Save promotor.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $data = $this->getDataFromRequest($request);

        // save promotor
        $promotor = $data['promotor'];

        $id = $this->service->save($promotor);

        return $this->app->json([
            'success' => true,
            'id' => $id
            ], 
            Response::HTTP_OK
        );
    }


    /**
     * Update promotor.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // promotor data
        $data = $this->getDataFromRequest($request);

        // update promotor
        $promotor = $data['promotor'];

        // update promotor
        $updated_promotor = [];
        if (isset($promotor['contacto_id'])) {
        	$updated_promotor['contacto_id'] = $promotor['contacto_id'];
        }
        if (isset($promotor['obra_id'])) {
            $updated_promotor['obra_id'] = $promotor['obra_id'];
        }
        if (isset($promotor['intervencion'])) {
        	$updated_promotor['intervencion'] = $promotor['intervencion'];
        }
       
        $this->service->update($id, $updated_promotor);

		return new JsonResponse(['success' => true]);
    }


    /**
     * Delete promotor.
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
            $promotor = $this->service->getById($id);
            if ($promotor) {
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
