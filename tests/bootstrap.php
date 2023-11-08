<?php

// setup autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// include common classes
require_once 'AbstractGatewayTestBase.php';
require_once 'utils/Assert.php';
require_once 'utils/Data.php';


define('GATEWAY_URL', getenv('API_URL') ?: 'https://api.shift4.com');
define('UPLOADS_URL', getenv('UPLOADS_URL') ?: 'https://uploads.api.shift4.com');
define('BACKOFFICE_URL', getenv('BACKOFFICE_URL') ?: 'https://dev.shift4.com');
define('SECRET_KEY', getenv('SECRET_KEY'));
define('PUBLIC_KEY', getenv('PUBLIC_KEY'));
