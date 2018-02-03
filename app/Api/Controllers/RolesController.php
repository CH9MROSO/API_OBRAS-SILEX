<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Roles Controller.
 */
class RolesController extends BaseController {
    
    /**
     * Constructor.
     */
    public function __construct($app, $service, $otherServices) {
        $this->app = $app;
        $this->service = $service;
        $this->otherServices = $otherServices;
        $this->permissions = [
            'getAll' => ['ROLE_SUPER_ADMIN']
        ];
    }
    
    /**
     * Get all roles.
     */
    public function getAll() {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        return new JsonResponse($this->service->getAll());
    }

}
