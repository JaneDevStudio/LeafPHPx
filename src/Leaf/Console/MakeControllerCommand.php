<?php

namespace Leaf\Console;

use Leaf\Console\Command;

class MakeControllerCommand extends Command
{
    public function getName(): string { return 'make:controller'; }
    public function getDescription(): string { return 'Create a new controller'; }

    public function execute(array $args): void
    {
        if (empty($args)) {
            echo "Usage: make:controller Name\n";
            return;
        }
        $name = $args[0];
        $file = __DIR__ . '/../../../app/Controllers/' . $name . '.php';
        $content = "<?php\nnamespace App\\Controllers;\n\nclass $name {\n    // Methods\n}\n";
        file_put_contents($file, $content);
        echo "Controller created: $file\n";
    }
}