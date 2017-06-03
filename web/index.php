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
                    'descriptions' => 'One or several of parameters has not be passed'
                ]
            ];
        }
        echo json_encode([
            'response' => $response
        ]);
        break;
    default:
        echo json_encode([
            'response' => [
                'error' => [
                    'code' => -1,
                    'descriptions' => 'Action is needed'
                ]
            ]
        ]);
}