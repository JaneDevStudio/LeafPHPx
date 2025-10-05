<?php

namespace Leaf\Console;

class Kernel
{
    protected array $commands = [];

    public function __construct()
    {
        // Register built-in commands
        $this->register(ServeCommand::class);
        $this->register(MakeControllerCommand::class);
        $this->register(MigrateCommand::class);
    }

    public function register(string $commandClass): void
    {
        $command = new $commandClass();
        $this->commands[$command->getName()] = $command;
    }

    public function handle(array $argv): void
    {
        if (count($argv) < 2) {
            $this->listCommands();
            return;
        }

        $commandName = $argv[1];
        if (!isset($this->commands[$commandName])) {
            echo "Command not found: $commandName\n";
            return;
        }

        $this->commands[$commandName]->execute(array_slice($argv, 2));
    }

    protected function listCommands(): void
    {
        echo "Available commands:\n";
        foreach ($this->commands as $name => $command) {
            echo "- $name: {$command->getDescription()}\n";
        }
    }
}