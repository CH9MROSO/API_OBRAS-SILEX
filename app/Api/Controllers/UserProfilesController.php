<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * UserProfiles Controller.
 */
class UserProfilesController extends BaseController {


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
            'paginatedSearch' => ['ROLE_SUPER_ADMIN'],
            'getById' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'getByUser' => ['ROLE_USUARIO'],
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
     * Get entity by Id.
     */
    public function getById($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $user_profile = $this->service->getById($id);
        
        $user = $this->getCurrentUser();
        if (in_array('ROLE_USUARIO', $user->getRoles())) {
            if ($user_profile['user_id'] != $user['id']) {
                return $this->app->abort(401, 'No tiene permiso para acceder a esta entidad.');
            }
        }

        return new JsonResponse($user_profile);
    }


    /**
     * Get entity by user id.
     */
    public function getByUser(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

       
        $user = $this->getCurrentUser();
        $user_profile = $this->service->getByUser($user->getId());
        
        if (!$user_profile) {
            return $this->app->abort(401, 'No tiene permiso para acceder a esta entidad.');
        }

        $user_id = 0;
        if (in_array('ROLE_USUARIO', $user->getRoles())) {
            $user_id = $user->getId();
        }

        return new JsonResponse($user_profile);
    }


    /**
     * Register user profile.
     * {
            "password": "10rizo40",
            "first_name": "Roberto",
            "surname": "Sánchez Lorenzo",
            "email": "rob.s.l@arquitecto.es",
            "birthday": "1980-06-15",
            "gender": "Masculino",
            "appointment": "Proyectista",
            "collage": "COADE-CC",
            "num_collage": "",
        } */
    public function register(Request $request) {
        // check user permissions on this function
        //$this->checkPermissions(__FUNCTION__);


        $user = $this->getDataFromRequest($request);
        //$user['username'] = $user['email'];

        $user_encoder = $this->app['users']->loadUserByUsername('admin');
        $encoder = $this->app['security.encoder_factory']->getEncoder($user_encoder);
        $encoded_password = $encoder->encodePassword($user['password'], $this->app['security.jwt']['secret_key']); 
       

        // save user
        $new_user = [
            'username' => $user['email'],
            'password' => $encoded_password,
            'name' => ($user['first_name'].' '.$user['surname']),
            'email' => $user['email'],
            'active' => 1,
            'verified' => 1
        ];
        $user_id = $this->otherServices['usersService']->save($new_user);
        if($user['role'] == 'Usuario'){
            $user['role'] = 'ROLE_USUARIO';
        }else if($user['role'] == 'admin'){
            $user['role']= 'ROLE_SUPER_ADMIN';
        }
        else{
            $user['role'] = 'ROLE_INVITADO';
        }


        // save role
        $role_id = intval($this->otherServices['rolesService']->getId($user['role']), 10);
        $user_role = [
            "user_id" => $user_id,
            "role_id" => $role_id
        ];
        $this->otherServices['rolesService']->saveUserRole($user_role);

        

        // save user_profile details
        $user_profile = [
            'user_id' => $user_id,
            'first_name' => $user['first_name'],
            'surname' => $user['surname'],
            'birthday' => $user['birthday'],
            'gender' => $user['gender'],
            'appointment' => $user['appointment'],
            'collage' => $user['collage'],
            'num_collage' => $user['num_collage'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        $user_profile_id = $this->service->save($user_profile);

        return new JsonResponse(["success" => true, "id" => $user_profile_id]);
    }


    /**
     * Update entity.
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        // entity data
        $data = $this->getDataFromRequest($request);

        // update entity
        $updated_user_profile = [];
        if (isset($data['first_name'])) {
        	$updated_user_profile['first_name'] = $data['first_name'];
        }
        if (isset($data['surname'])) {
            $updated_user_profile['surname'] = $data['surname'];
        }
        if (isset($data['birthday'])) {
        	$updated_user_profile['birthday'] = $data['birthday'];
        }
        if (isset($data['gender'])) {
        	$updated_user_profile['gender'] = $data['gender'];
        }
        if (isset($data['appointment'])) {
            $updated_user_profile['appointment'] = $data['appointment'];
        }
        if (isset($data['collage'])) {
            $updated_user_profile['collage'] = $data['collage'];
        }
        if (isset($data['num_collage'])) {
            $updated_user_profile['num_collage'] = $data['num_collage'];
        }
        if (isset($data['email'])) {
            $updated_user_profile['email'] = $data['email'];
        }

        $this->service->update($id, $updated_user_profile);
        


        // update user
        if (!empty($data['first_name']) OR !empty($data['surname']) OR !empty($data['email'])) {
            $user_profile = $this->service->getById($id);
            if (!empty($data['first_name']) OR !empty($data['surname'])) {
                $user_profile_user = [
                    'name' => ($data['first_name'].' '.$data['surname']),
                ];
            }
            if (!empty($data['email'])) {
                $user_profile_user = [
                    'email' => $data['email'],
                    'username' => $data['email']
                ];
            }
            
            $this->otherServices['usersService']->update($user_profile['user_id'], $user_profile_user);
        }

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
                'error' => 'Este perfil de usuario no se puede eliminar porque hay otros datos relacionados con él.'
            ];             
            return $this->app->json($response, Response::HTTP_OK);
        } else {
            $user_profile = $this->service->getById($id);
            $user_id = $user_profile['user_id'];
            $this->service->delete($id);
            $this->otherServices['usersService']->delete($user_id);
            $this->otherServices['rolesService']->deleteUserRoles($user_id);
            $this->otherServices['settingsService']->deleteUserSettings($user_id);


            return new JsonResponse(['success' => true]);
        }
    }

}
