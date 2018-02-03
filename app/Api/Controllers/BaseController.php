<?php
namespace Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Base Controller.
 */
class BaseController {

    // main service object
    protected $service;

    // array of other service objects
    protected $otherServices;

    // application object
    protected $app;

    // permissions array
    protected $permissions;

    // search params
    protected $search_params;

    // search fields
    protected $search_fields;


    /**
     * Build search params.
     */
    protected function buildSearchParams($function_name, Request $request, $extra_params) {
        // search and sort base params
        $this->search_params = [
            'page' => $request->get('page'),
            'page_size' => $request->get('page_size'),
            'total' => $request->get('total'),
            'sort_field' => $request->get('sort_field'),
            'sort_dir' => $request->get('sort_dir'),
            'search_fields' => []
        ];


        // search fields
        if (is_array($this->search_fields)) {
            // for every roles
            if (isset($this->search_fields[$function_name])) {
                foreach ($this->search_fields[$function_name]['*'] as $field) {
                    $this->search_params['search_fields'][$field] = $request->get($field);
                }
            }

            // for user roles
            $user = $this->getCurrentUser();
            $roles = $user->getRoles();
            foreach ($roles as $rol) {
                foreach ($this->search_fields[$function_name][$rol] as $field) {
                    $this->search_params['search_fields'][$field] = $request->get($field);
                }
            }
        }

        // extra params
        foreach ($extra_params as $key => $value) {
            $this->search_params['search_fields'][$key] = $value;
        }

    }


    /**
     * Get data from request in JSON format.
     */
    protected function getDataFromRequest(Request $request) {
        $data = json_decode($request->getContent(), true);
        return $data;
    }


    /**
     * Return a ParameterBag with the form data.
     */
    protected function getFormData(Request $request) {
        return $request->request;
    }


    /**
     * Return a FileBag with form files data.
     */
    protected function getFileData(Request $request) {
        return $request->files;
    }


    /**
     * Removes tildes and specials signs.
     */
    protected function normalize($str) {
        $unwanted_array = [
            'Š'=>'S', 'š'=>'s', 
            'Ž'=>'Z', 'ž'=>'z', 
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 
            'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 
            'Ñ'=>'N', 
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 
            'Ý'=>'Y', 
            'Þ'=>'B', 'ß'=>'Ss', 
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 
            'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
            'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 
            'ð'=>'o', 
            'ñ'=>'n', 
            'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 
            'ý'=>'y', 
            'þ'=>'b', 
            'ÿ'=>'y'
        ];
        
        return strtr($str, $unwanted_array);
    }


    /**
     * Send email.
     *
     * @param $from array Sender email array
     * @param $to array Recipient emails array 
     * @param $subject string Message subject
     * @param $message_template string string Twig template name
     * @param $message_template_params array Twig template params
     */
    protected function sendEmail($from, $to, $subject, $message_template, $message_template_params, $images = []) {
        $message = \Swift_Message::newInstance();
        echo("---------Dentro del sendEmail------------\n");
        if (!empty($images)) {
            foreach ($images as $image_name => $image_path) {
                $message_template_params[$image_name] = $message->embed(\Swift_Image::fromPath($image_path));
            }
        }
        echo("---------Despues del if y el foreach-----------\n");
        $message->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->app['twig']->render($message_template, $message_template_params),
                'text/html'
            );
        
        echo("---------Despues del set subject-----------\n");
        
        $numSent = $this->app['mailer']->send($message);

        echo("------------estamos aqui-------------\n");

        return $numSent;
    }


    /**
     * Encode password.
     */
    protected function encodePassword($password) {
        $token = $this->app['security.token_storage']->getToken();
        //$token = $this->app['security']->getToken();
        $user = $token->getUser();      
        $encoder = $this->app['security.encoder_factory']->getEncoder($user);
        $encoded_password = $encoder->encodePassword($password, $this->app['security.jwt']['secret_key']);

        return $encoded_password;        
    }


    /**
     * Generate random password.
     */
    protected function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#.-_()!';
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }


    /**
     * Get current user
     */
    public function getCurrentUser() {
        $token = $this->app['security.token_storage']->getToken();
        $user = $token->getUser();
        return $user;
    }


    /**
     * Check user permissions on this function.
     */
    protected function checkPermissions($function_name) {
        if ($this->app['security.authorization_checker']->isGranted($this->permissions[$function_name])) {
            return true;
        } else {
            $this->app->abort(401, 'No tiene permiso para acceder a esta funcionalidad.');
        }
    }

}
