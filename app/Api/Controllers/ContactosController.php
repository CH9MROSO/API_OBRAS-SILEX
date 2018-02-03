<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Contactos Controller.
 */
class ContactosController extends BaseController {


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
     * Get all contactos.
     *
     */ 
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $contactos = $this->service->getAll();

        if ($contactos){
            $data = [
                'success' => true,
                'contactos' => $contactos
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
     * Get contacto by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $contacto = $this->service->getById($id);
        
/*         $user = $this->getCurrentUser(); */

        if ($contacto){
            $data = [
                'success' => true,
                'contacto' => $contacto
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
     * Save Contacto.
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $data = $this->getDataFromRequest($request);


        // save contacto
        $contacto = $data['contacto'];

        $id = $this->service->save($contacto);


        return $this->app->json([
                'success' => true,
                'id' => $id
            ], 
            Response::HTTP_OK
        );
    }


    /**
     * Update contacto.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // contacto data
        $data = $this->getDataFromRequest($request);

        // update contacto
        $contacto = $data['contacto'];

        $updated_contacto = [];
        if (isset($contacto['id'])) {
        	$updated_contacto['id'] = $contacto['id'];
        }
        if (isset($contacto['dni_cif'])) {
            $updated_contacto['dni_cif'] = $contacto['dni_cif'];
        }
        if (isset($contacto['nombre_razon'])) {
            $updated_contacto['nombre_razon'] = $contacto['nombre_razon'];
        }
        if (isset($contacto['apellidos'])) {
        	$updated_contacto['apellidos'] = $contacto['apellidos'];
        }
        if (isset($contacto['direccion'])) {
        	$updated_contacto['direccion'] = $contacto['direccion'];
        }
        if (isset($contacto['cp'])) {
            $updated_contacto['cp'] = $contacto['cp'];
        }
        if (isset($contacto['municipio'])) {
            $updated_contacto['municipio'] = $contacto['municipio'];
        }
        if (isset($contacto['Provincia'])) {
            $updated_contacto['Provincia'] = $contacto['Provincia'];
        }
        if (isset($contacto['Pais'])) {
            $updated_contacto['Pais'] = $contacto['Pais'];
        }
        if (isset($contacto['email'])) {
            $updated_contacto['email'] = $contacto['email'];
        }
        if (isset($contacto['telefono'])) {
            $updated_contacto['telefono'] = $contacto['telefono'];
        }
        if (isset($contacto['tipo_persona_juridica'])) {
            $updated_contacto['tipo_persona_juridica'] = $contacto['tipo_persona_juridica'];
        }
        if (isset($contacto['representante'])) {
            $updated_contacto['representante'] = $contacto['representante'];
        }
        if (isset($contacto['profilePic'])) {
            $updated_contacto['profilePic'] = $contacto['profilePic'];
        }
       
        $this->service->update($id, $updated_contacto);

		return new JsonResponse(['success' => true]);
    }


    /**
     * Delete contacto.
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
            $contacto = $this->service->getById($id);
            if ($contacto) {
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
