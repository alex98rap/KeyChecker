<?php
/**
 * Created by PhpStorm.
 * User: alex1rap
 * Date: 03.06.2017
 * Time: 15:38
 */

require_once __DIR__ . '/../vendor/autoload.php';

use RAP\keys\KeyManager;

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : null;
$app = isset($_REQUEST['app']) ? $_REQUEST['app'] : null;
switch ($action) {
    case 'check':
        if ($key !== null && $app !== null) {
            $result = KeyManager::check($app, $key);
            $response = ($result !== false) ? [
                'success' => [
                    'key_id' => $result->key_id,
                    'key_text' => $result->key_text,
                    'app_id' => $result->app_id,
                    'activated_time' => $result->activated_time,
                    'descriptions' => 'Program is registered'
                ]
            ] : [
                'error' => [
                    'code' => 99,
                    'descriptions' => 'Program is not registered'
                ]
            ];
        } else {
            $response = [
                'error' => [
                    'code' => 1,
                    'descriptions' => 'One or several of parameters is not be passed'
                ]
            ];
        }
        break;
    case 'register':
        if ($key !== null) {
            $app = KeyManager::register($key);
            if ($app !== false) {
                $response = [
                    'success' => [
                        'key_id' => $app->key_id,
                        'key_text' => $app->key_text,
                        'app_id' => $app->app_id,
                        'is_activated' => $app->is_activated,
                        'activated_time' => $app->activated_time,
                        'descriptions' => 'Application is successfully registered'
                    ]
                ];
            } else {
                $response = [
                    'error' => [
                        'code' => 99,
                        'descriptions' => 'Application cannot be registered: key already activated or doesn\'t exists'
                    ]
                ];
            }
        } else {
            $response = [
                'error' => [
                    'code' => 1,
                    'descriptions' => 'One or several of parameters is not be passed'
                ]
            ];
        }
        break;
    default:
        $response = [
            'error' => [
                'code' => -1,
                'descriptions' => 'Action is needed'
            ]
        ];
        break;
}
echo json_encode([
    'response' => $response
]);