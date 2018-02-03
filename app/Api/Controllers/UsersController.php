<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Api\Providers\UserProvider;
use Api\Providers\User;


/**
 * Users controller.
 */
class UsersController extends BaseController {

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
            'login' => ['ROLE_SUPER_ADMIN'],
            'logout' => ['ROLE_SUPER_ADMIN'],
            'paginatedSearch' => ['ROLE_SUPER_ADMIN'],
            'getById' => ['ROLE_SUPER_ADMIN'],
            'save' => ['ROLE_SUPER_ADMIN'],
            'update' => ['ROLE_SUPER_ADMIN','ROLE_USUARIO'],
            'delete' => ['ROLE_SUPER_ADMIN'],
            'activate' => ['ROLE_SUPER_ADMIN'],
            'deactivate' => ['ROLE_SUPER_ADMIN'],
            'changePassword' => ['ROLE_SUPER_ADMIN', 'ROLE_USUARIO'],
            'verifyToken' => ['ROLE_SUPER_ADMIN', 'ROLE_USUARIO']
        ];
    }

    
    /**
     * Login.
     *
     * Input:
     * {
     *      _username: "nombre de usuario",
     *      _password: "clave"
     * }
     */
    public function login(Request $request) {

        /*$vars = $this->getDataFromRequest($request);
        $clave = $vars['_password'];
        $user = $this->app['users']->loadUserByUsername($vars['_username']);
        $encoder = $this->app['security.encoder_factory']->getEncoder($user);
        $encrypted = $encoder->encodePassword($clave, $this->app['security.jwt']['secret_key']);
        return $this->app->json(['clave' => $encrypted], Response::HTTP_OK);*/

//-----------------------------------------------------

        $vars = $this->getDataFromRequest($request);
     
        try {
            
            if (empty($vars['_username']) || empty($vars['_password'])) {
                throw new UsernameNotFoundException(sprintf('El usuario "%s" no existe.', $vars['_username']));
            }
            /**
             * @var $user User
             */
            $user = $this->app['users']->loadUserByUsername($vars['_username']);


            if (!$user->isActive()) {
                throw new UsernameNotFoundException(sprintf('El usuario "%s" no está activo.', $vars['_username']));
            }/* elseif (!$this->service->isValidPassword($vars['_username'], $vars['_password'])) {
                throw new UsernameNotFoundException(sprintf('Contraseña incorrecta para el usuario "%s"', $vars['_username']));
            }*/ else if (! $this->app['security.encoder.digest']->isPasswordValid($user->getPassword(), $vars['_password'], $this->app['security.jwt']['secret_key'])) {
                throw new UsernameNotFoundException(sprintf('El usuario "%s" no existe.', $vars['_username']));
            } else {
                $response = [
                    'success' => true,
                    'token' => $this->app['security.jwt.encoder']->encode(['name' => $user->getUsername()]),
                    'username' => $user->getUsername(),
                    'name' => $user->getName(),
                    'roles' => $user->getRoles()
                ];
            }
        
            
        } catch (UsernameNotFoundException $e) {
            $response = [
                'success' => false,
                'error' => 'Los datos de acceso no son válidos para el usuario '.$vars['_username'].';'.$e->getMessage(),
            ];
        }

        return $this->app->json($response, Response::HTTP_OK);
    }
    

    /**
     * Logout.
     */
    public function logout(Request $request) {
        $this->app['security.token_storage']->setToken(null);
        $response = [
            'success' => true
        ];
        return $this->app->json($response, ($response['success'] == true ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
    }
    

    /**
     * Get all users.
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
     * Get user by id.
     */
    public function getById($id) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);


        $user = $this->service->getById($id);
        
        return new JsonResponse($user);
    }


    
    /**
     * Create user.
     *
     * Input:
     * {
     *      name: "nombre"
     *      username: "nombre de usuario"
     *      email: "email"
     *      password: "clave"
     *      active: 1
     *      verified: 1
     * }
     */
    public function save(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $user = $this->getDataFromRequest($request);

        // save user
        $new_user = [
            'username' => ($user['is_user_profile']) ? $user['email'] : $user['username'],
            'password' => $this->encodePassword($user['password']),
            'name' => ($user['is_user_profile']) ? ($user['first_name'].' '.$user['surname']) : $user['name'],
            'email' => $user['email'],
            'active' => 1,
            'verified' => 1
        ];
        $user_id = $this->service->save($new_user);

        // save role
        foreach ($user['roles'] as $role) {
            $role_id = 0;
            if (is_numeric($role)) {
                $role_id = intval($role, 10);
            } else {
                $role_id = intval($this->otherServices['rolesService']->getId($role), 10);
            }

            if ($role_id) {
                $user_role = [
                    "user_id" => $user_id,
                    "role_id" => $role_id
                ];
                $this->otherServices['rolesService']->saveUserRole($user_role);
            }
        }


        // save user profile details
        if ($user['is_user_profile']) {
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
            ];

            $this->otherServices['user_profilesService']->save($user_profile);

        }

        return new JsonResponse(["id" => $user_id]);
    }

    
    /**
     * Update user.
     *
     * Input:
     * {
     *      name: "nombre miembro junta electoral"
     *      username: "nombre de usuario"
     *      email: "email"
     * }
     */
    public function update($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $user = $this->getDataFromRequest($request);

        $this->service->update($id, $user);
        return new JsonResponse($user);
    }

    
    /**
     * Delete user.
     */
    public function delete($id) {
        if ($id != 1) {
            // check user permissions on this function
            $this->checkPermissions(__FUNCTION__);

            if ($this->service->hasDependencies($id)) {
                $response = [
                    'success' => false,
                    'error' => 'Este usuario no se puede eliminar porque hay otros datos relacionados con él.'
                ];             
                return $this->app->json($response, Response::HTTP_OK);
            } else {

                $this->otherServices['rolesService']->deleteUserRoles($id);
                $this->otherServices['settingsService']->deleteUserSettings($id);
                $this->otherServices['user_profilesService']->deleteByUserId($id);

                return new JsonResponse($this->service->delete($id));
            }              

        } else {
            throw new \Exception('Este usuario no puede eliminarse');
        }        
    }


    /**
     * Activate user.
     */
    public function activate($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $this->service->update($id, ['active' => 1]);
        return new JsonResponse($user);
    }


    /**
     * Deactivate user.
     */
    public function deactivate($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $this->service->update($id, ['active' => 0]);
        return new JsonResponse($user);
    }


    /**
     * Change password for super administrator.
     *
     * Input:
     * {
     *      password: "clave"
     * }
     */
    public function changePassword($id, Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $user_password = $this->getDataFromRequest($request);

        // encode password
        $encoded_password = $this->encodePassword($user_password['password']);
        //$encoded_password = $user_password['password'];
        
        $user_updates = [
            'id' => $id,
            'password' => $encoded_password
        ];

        $updated = $this->service->update($id, $user_updates);

        $response = ['success' => ($updated == 1)];
        return $this->app->json($response, ($response['success'] == true ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
    }


    /**
     * Verify a valid user token.
     */
    public function verifyToken() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $user = $this->getCurrentUser();

        $response = [
            'success' => true
        ];
        return $this->app->json($response, Response::HTTP_OK);        
    }
    
}
