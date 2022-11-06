<?php

declare(strict_types=1);

namespace App;

use App\Entity\SomeEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('some-command')]
final class SomeCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly SomeService $someService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->managerRegistry->getManager();
        $entity = $manager->find(SomeEntity::class, 1);
        if ($entity === null) {
            $manager->persist(new SomeEntity());
            $manager->flush();
        }

        $output->writeln('First execution');
        $this->someService->doSomething();

        // This happens during the container reset after processing a message when the manager is closed (e.g. due to a deadlock exception)
        $this->managerRegistry->resetManager();

        $output->writeln('Second execution after reset');
        $this->someService->doSomething();

        return 0;
    }
}
