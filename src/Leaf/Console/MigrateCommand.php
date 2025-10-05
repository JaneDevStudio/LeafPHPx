<?php

namespace Leaf\Console;

use Leaf\Console\Command;

class MigrateCommand extends Command
{
    public function getName(): string { return 'migrate'; }
    public function getDescription(): string { return 'Run database migrations'; }

    public function execute(array $args): void
    {
        // Implement migration logic
        echo "Migrations run.\n";
    }
}