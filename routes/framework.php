<?php

use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Route;

$appType = app_type();
$routeArray = [
    'namespace' => 'App\Http\Controllers\Api\Common',
];

if ($appType == 'saas') {
    $routeArray['prefix'] = 'superadmin';
}

if ($appType == 'non-saas') {
    $routeArray['middleware'] = ['api.permission.check', 'api.auth.check', 'license-expire'];
} else {
    $routeArray['middleware'] = ['api.superadmin.check', 'license-expire'];
}

// ** Framework routes group ** //
ApiRoute::group($routeArray, function () {
    // Excute a SQL query
    ApiRoute::post('execute-query', ['as' => 'api.framework.execute', 'uses' => 'FrameworkController@executeQuery']);

    // Audit actions
    ApiRoute::post('audit', ['as' => 'api.framework.audit', 'uses' => 'FrameworkController@audit']);

    // Upload files
    ApiRoute::post('upload-file', ['as' => 'api.framework.uploadFile', 'uses' => 'FrameworkController@uploadFile']);

});
// ** End framework routes group ** //
