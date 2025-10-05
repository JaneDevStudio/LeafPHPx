<?php

namespace Leaf\Console;

class ServeCommand extends Command
{
    public function getName(): string        { return 'serve'; }
    public function getDescription(): string { return 'Start the built-in PHP server'; }

    public function execute(array $args): void
    {
        $host = $args[0] ?? '0.0.0.0';
        $port = $args[1] ?? '8000';
        $docRoot = __DIR__ . '/../../../public';

        echo "Development server started at http://$host:$port\n";
        echo "Document root: $docRoot\n";
        echo "Press Ctrl-C to stop.\n\n";

        passthru("php -S $host:$port -t $docRoot");
    }
}
