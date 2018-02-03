<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Obra Controller.
 */
class ObrasController extends BaseController {


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
     * Get all Obras.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $obras = $this->service->getAll();
        
/*         $user = $this->getCurrentUser(); */

        if ($obras){
            $data = [
                'success' => true,
                'obras' => $obras
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
     * Get Obra by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $obra = $this->service->getById($id);

        if ($obra){
            $data = [
                'success' => true,
                'obra' => $obra
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
     * Save Obra.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $data = $this->getDataFromRequest($request);


        // save obra
        $obra = $data['obra'];
    /*  foreach ($agentes as $agente) {
            

            $agente_obra = [
                'obra_id' => $obra['id'],
                'agente_id' => $agente['id'],
            ];

            $this->service->saveAgente($agente_obra);
        } */

        $id = $this->service->save($obra);


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

        // update obra
        $obra = $data['obra'];

        $updated_obra = [];
        if (isset($obra['id'])) {
        	$updated_obra['id'] = $obra['id'];
        }
        if (isset($obra['fecha_inicio'])) {
            $updated_obra['fecha_inicio'] = $obra['fecha_inicio'];
        }
        if (isset($obra['fecha_fin'])) {
            $updated_obra['fecha_fin'] = $obra['fecha_fin'];
        }
        if (isset($obra['descripcion'])) {
        	$updated_obra['descripcion'] = $obra['descripcion'];
        }
        if (isset($obra['ubicacion'])) {
        	$updated_obra['ubicacion'] = $obra['ubicacion'];
        }
        if (isset($obra['estado'])) {
            $updated_obra['estado'] = $obra['estado'];
        }
        if (isset($obra['profilePic'])) {
            $updated_obra['profilePic'] = $obra['profilePic'];
        }
       
        $this->service->update($id, $updated_obra);

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
            $obras = $this->service->getById($id);
            if ($obras) {
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
