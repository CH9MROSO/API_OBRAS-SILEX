<?php
namespace Api;

use Silex\Application;


/**
 * Controllers classes loader.
 */
class RoutesLoader {
    
    private $app;

    
    /**
     * Constructor.
     */
    public function __construct(Application $app) {
        $this->app = $app;
        $this->instantiateControllers();
    }
    
    /**
     * Controller instances.
     */
    private function instantiateControllers() {
        $this->app['users.controller'] = $this->app->share(function () {
            return new Controllers\UsersController(
                $this->app, $this->app['users.service'], 
                [
                    'rolesService' => $this->app['roles.service'],
                    'user_profilesService' => $this->app['user_profiles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['roles.controller'] = $this->app->share(function () {
            return new Controllers\RolesController(
                $this->app, $this->app['roles.service'], 
                []
            );
        });

        $this->app['settings.controller'] = $this->app->share(function () {
            return new Controllers\SettingsController(
                $this->app, $this->app['settings.service'], 
                []
            );
        });

        $this->app['user_profiles.controller'] = $this->app->share(function () {
            return new Controllers\UserProfilesController(
                $this->app, $this->app['user_profiles.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['obras.controller'] = $this->app->share(function () {
            return new Controllers\ObrasController(
                $this->app, $this->app['obras.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['contactos.controller'] = $this->app->share(function () {
            return new Controllers\contactosController(
                $this->app, $this->app['contactos.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['clientes.controller'] = $this->app->share(function () {
            return new Controllers\ClientesController(
                $this->app, $this->app['clientes.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['promotores.controller'] = $this->app->share(function () {
            return new Controllers\PromotoresController(
                $this->app, $this->app['promotores.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['tecnicos.controller'] = $this->app->share(function () {
            return new Controllers\TecnicosController(
                $this->app, $this->app['tecnicos.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['constructores.controller'] = $this->app->share(function () {
            return new Controllers\ConstructoresController(
                $this->app, $this->app['constructores.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });

        $this->app['subcontratistas.controller'] = $this->app->share(function () {
            return new Controllers\SubcontratistasController(
                $this->app, $this->app['subcontratistas.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });
        $this->app['visitas.controller'] = $this->app->share(function () {
            return new Controllers\VisitasController(
                $this->app, $this->app['visitas.service'], 
                [
                    'usersService' => $this->app['users.service'],
                    'rolesService' => $this->app['roles.service'],
                    'settingsService' => $this->app['settings.service']
                ]
            );
        });
    }
    

    /**
     * Routes.
     */
    public function bindRoutesToControllers() {
        $api = $this->app["controllers_factory"];
        
        // users
        $api->post('/login', "users.controller:login");
        $api->get('/logout', "users.controller:logout");
        $api->get('/usuarios', "users.controller:paginatedSearch");
        $api->get('/usuarios/verificar_token', "users.controller:verifyToken");
        $api->get('/usuarios/{id}', "users.controller:getById");
        $api->post('/usuarios', "users.controller:save");
        $api->post('/usuarios/{id}', "users.controller:update");
        $api->post('/usuarios/activate/{id}', "users.controller:activate");
        $api->post('/usuarios/deactivate/{id}', "users.controller:deactivate");
        $api->post('/usuarios/change_password/{id}', "users.controller:changePassword");
        $api->post('/usuarios/eliminar/{id}', "users.controller:delete");

        // roles
        $api->get('/roles', 'roles.controller:getAll');

        // settings
        $api->get('/configuraciones', 'settings.controller:getSettings');
        $api->post('/configuraciones/usuario', 'settings.controller:setSettings');
        // User profiles details
        $api->get('/perfiles', 'user_profiles.controller:paginatedSearch');
        $api->get('/perfiles/{id}', 'user_profiles.controller:getById');
        $api->get('/perfil', 'user_profiles.controller:getByUser');
        $api->post('/perfiles/eliminar/{id}', 'user_profiles.controller:delete');
        $api->post('/perfiles/registrar', 'user_profiles.controller:register');
        $api->post('/perfiles/{id}', "user_profiles.controller:update");
        // obras
        $api->get('/obras', 'obras.controller:getAll');
        $api->get('/obras/{id}', 'obras.controller:getById');
        $api->post('/obras/nueva', 'obras.controller:save');
        $api->post('/obras/{id}', "obras.controller:update");
        $api->post('/obras/eliminar/{id}', 'obras.controller:delete');
        // contactos
        $api->get('/contactos', 'contactos.controller:getAll');
        $api->get('/contactos/{id}', 'contactos.controller:getById');
        $api->post('/contactos/nuevo', 'contactos.controller:save');
        $api->post('/contactos/{id}', "contactos.controller:update");
        $api->post('/contactos/eliminar/{id}', 'contactos.controller:delete');
        // clientes
        $api->get('/clientes', 'clientes.controller:getAll');
        $api->get('/clientes/Obra/{idObra}', 'clientes.controller:getAllByIdObra');
        $api->get('/clientes/{id}', 'clientes.controller:getById');
        $api->post('/clientes/nuevo', 'clientes.controller:save');
        $api->post('/clientes/{id}', "clientes.controller:update");
        $api->post('/clientes/eliminar/{id}', 'clientes.controller:delete');
        // promotores
        $api->get('/promotores', 'promotores.controller:getAll');
        $api->get('/promotores/Obra/{idObra}', 'promotores.controller:getAllByIdObra');
        $api->get('/promotores/{id}', 'promotores.controller:getById');
        $api->post('/promotores/nuevo', 'promotores.controller:save');
        $api->post('/promotores/{id}', "promotores.controller:update");
        $api->post('/promotores/eliminar/{id}', 'promotores.controller:delete');
        // tecnicos
        $api->get('/tecnicos', 'tecnicos.controller:getAll');
        $api->get('/tecnicos/Obra/{idObra}', 'tecnicos.controller:getAllByIdObra');
        $api->get('/tecnicos/{id}', 'tecnicos.controller:getById');
        $api->post('/tecnicos/nuevo', 'tecnicos.controller:save');
        $api->post('/tecnicos/{id}', "tecnicos.controller:update");
        $api->post('/tecnicos/eliminar/{id}', 'tecnicos.controller:delete');
        // constructores
        $api->get('/constructores', 'constructores.controller:getAll');
        $api->get('/constructores/Obra/{idObra}', 'constructores.controller:getAllByIdObra');
        $api->get('/constructores/{id}', 'constructores.controller:getById');
        $api->post('/constructores/nuevo', 'constructores.controller:save');
        $api->post('/constructores/{id}', "constructores.controller:update");
        $api->post('/constructores/eliminar/{id}', 'constructores.controller:delete');
        // subcontratistas
        $api->get('/subcontratistas', 'subcontratistas.controller:getAll');
        $api->get('/subcontratistas/Obra/{idObra}', 'subcontratistas.controller:getAllByIdObra');
        $api->get('/subcontratistas/{id}', 'subcontratistas.controller:getById');
        $api->post('/subcontratistas/nuevo', 'subcontratistas.controller:save');
        $api->post('/subcontratistas/{id}', "subcontratistas.controller:update");
        $api->post('/subcontratistas/eliminar/{id}', 'subcontratistas.controller:delete');
        // visitas
        $api->get('/visitas', 'visitas.controller:getAll');
        $api->get('/visitas/Obra/{idObra}', 'visitas.controller:getAllByIdObra');
        $api->get('/visitas/{id}', 'visitas.controller:getById');
        $api->post('/visitas/nueva', 'visitas.controller:save');
        $api->post('/visitas/{id}', "visitas.controller:update");
        $api->post('/visitas/eliminar/{id}', 'visitas.controller:delete');
        


        // add andpoint and version to routes.
        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }
    
}