<?php

declare(strict_types=1);

namespace App\Commands;

use App\Http\ServiceClient;
use Symfony\Component\Console\Command\Command;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $encoder = (new BufferJsonEncoder(function () {
            foreach ($this->serviceHttpClient->all() as $item) {
                $service = [
                    'id' => $item->id,
                ];
                foreach (['fi', 'en', 'sv'] as $language) {
                    $service[$language] = $this->serviceHttpClient->get($item->id, $language) ?? [];
                }
                yield $service;
            }
        }))->setOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $output->write($encoder->encode());
        return Command::SUCCESS;
    }
}
