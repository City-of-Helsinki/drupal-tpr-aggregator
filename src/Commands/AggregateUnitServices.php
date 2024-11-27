<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:aggregate-unit-services')]
final class AggregateUnitServices extends Command
{
    public function configure()
    {
        $this->addArgument(
            'servicesFile',
            InputArgument::REQUIRED,
            'Previously generated services data file.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = $input->getArgument('servicesFile');

        if (!file_exists($fileName)) {
            throw new \InvalidArgumentException(sprintf('Service file %s not found.', $fileName));
        }
        // Generate a list of units containing a list of services provided in
        // that given unit.
        $data = json_decode(file_get_contents($fileName), true);

        $services = [];

        foreach ($data as $item) {
            foreach ($item['fi']['unit_ids'] ?? [] as $unitId) {
                $services[$unitId]['unit_id'] = $unitId;
                $services[$unitId]['services'][] = $item['fi']['id'];
            }
        }
        $output->write(json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return Command::SUCCESS;
    }
}
