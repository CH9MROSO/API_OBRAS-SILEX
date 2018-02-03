<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Settings Controller.
 */
class SettingsController extends BaseController {

	/**
     * Constructor.
     */
    public function __construct($app, $service, $otherServices) {
        $this->app = $app;
        $this->service = $service;
        $this->otherServices = $otherServices;
        $this->search_fields = [];
        $this->permissions = [
            'getSettings' => ['ROLE_SUPER_ADMIN', 'ROLE_USUARIO'],
            'setSettings' => ['ROLE_USUARIO']
        ];
    }


    /**
     * Get all settings.
     */
    public function getSettings(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $user_id = 0;
        $user = $this->getCurrentUser();
        if (in_array('ROLE_USUARIO', $user->getRoles())) {
            $user_id = $user->getId();
        }
        $settings = $this->service->getSettings($user_id);
        
        return new JsonResponse($settings);
    }


    /**
     * Set user settings.
     */
    public function setSettings(Request $request) {
        // check user permissions on this function
        $this->checkPermissions(__FUNCTION__);

        $user = $this->getCurrentUser();
        $user_id = $user->getId();


        // entity data
        $data = $this->getDataFromRequest($request);
        foreach ($data['settings'] as $new_setting) {
            $setting = $this->service->getSettingByAlias($new_setting['alias'], $user_id);

            $this->service->updateUserSetting($setting['id'], $user_id, $new_setting['setting_value']);
        }

        return new JsonResponse(['success' => true]);
    }

}
