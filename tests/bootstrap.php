<?php

// setup autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// include common classes
require_once 'AbstractGatewayTest.php';
require_once 'utils/Assert.php';
require_once 'utils/Data.php';


define('GATEWAY_URL', getenv('integrationTests.gateway.url') ?: 'https://api.shift4.com');
define('UPLOADS_URL', getenv('integrationTests.gateway.uploadsUrl') ?: 'https://uploads.api.shift4.com');
define('BACKOFFICE_URL', getenv('integrationTests.backOffice.url') ?: 'https://dev.shift4.com');
define('PRIVATE_KEY', getenv('integrationTests.privateKey'));
define('PUBLIC_KEY', getenv('integrationTests.publicKey'));
