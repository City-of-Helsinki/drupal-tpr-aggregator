<?php

declare(strict_types=1);

namespace App\Commands;

use App\Http\ServiceClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Violet\StreamingJsonEncoder\BufferJsonEncoder;

final class AggregateTprServiceCommand extends Command
{
    protected static $defaultName = 'app:aggregate-tpr-services';

    public function __construct(private ServiceClient $serviceHttpClient)
    {
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument(
            'endpoint',
            InputArgument::REQUIRED,
            'The endpoint to fetch data from. Available values: description, errandservice'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoint = $input->getArgument('endpoint');
        $encoder = (new BufferJsonEncoder(
            function () use ($endpoint) {
                foreach ($this->serviceHttpClient->all($endpoint) as $item) {
                    $serviceGroup = [
                        'id' => $item['id'],
                    ];
                    foreach (['fi', 'en', 'sv'] as $language) {
                        $serviceGroup[$language] = $this
                            ->serviceHttpClient
                            ->get($endpoint, $item['id'], $language);
                    }
                    yield $serviceGroup;
                }
            }
        ))->setOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $output->write($encoder->encode());

        return Command::SUCCESS;
    }
}
