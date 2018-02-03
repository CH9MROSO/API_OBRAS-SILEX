<?php
namespace Api;

use Silex\Application;


/**
 * Services classes loader.
 */
class ServicesLoader {

    protected $app;

    
    /**
     * Constructor
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }
    

    /**
     * Services
     */
    public function bindServicesIntoContainer() {
        $this->app['users.service'] = $this->app->share(function () {
            return new Services\UsersService($this->app);
        });

        $this->app['roles.service'] = $this->app->share(function () {
            return new Services\RolesService($this->app);
        });

        $this->app['settings.service'] = $this->app->share(function () {
            return new Services\SettingsService($this->app);
        });

        $this->app['user_profiles.service'] = $this->app->share(function () {
            return new Services\UserProfilesService($this->app);
        });

        $this->app['obras.service'] = $this->app->share(function () {
            return new Services\ObrasService($this->app);
        });

        $this->app['contactos.service'] = $this->app->share(function () {
            return new Services\ContactosService($this->app);
        });

        $this->app['clientes.service'] = $this->app->share(function () {
            return new Services\ClientesService($this->app);
        });

        $this->app['promotores.service'] = $this->app->share(function () {
            return new Services\PromotoresService($this->app);
        });

        $this->app['tecnicos.service'] = $this->app->share(function () {
            return new Services\TecnicosService($this->app);
        });

        $this->app['constructores.service'] = $this->app->share(function () {
            return new Services\ConstructoresService($this->app);
        });

        $this->app['subcontratistas.service'] = $this->app->share(function () {
            return new Services\SubcontratistasService($this->app);
        });
        $this->app['visitas.service'] = $this->app->share(function () {
            return new Services\VisitasService($this->app);
        });
    }
    
}
