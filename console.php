<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Commands\AggregateTprServiceCommand;
use App\Http\ServiceClient;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application;

$serviceHttpClient = new ServiceClient(new Client(['base_uri' => ServiceClient::BASE_URI]));
$application = new Application();
$application->add(new AggregateTprServiceCommand($serviceHttpClient));
$application->run();
