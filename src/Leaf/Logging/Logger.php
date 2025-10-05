<?php

namespace Leaf\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    protected MonologLogger $logger;

    public function __construct()
    {
        $this->logger = new MonologLogger('leaf');
        $path = \Leaf\Config::get('logging.channels.file.path');
        $this->logger->pushHandler(new StreamHandler($path));
    }

    // Delegate all methods to Monolog
    public function emergency($message, array $context = []) { $this->logger->emergency($message, $context); }
    public function alert($message, array $context = []) { $this->logger->alert($message, $context); }
    public function critical($message, array $context = []) { $this->logger->critical($message, $context); }
    public function error($message, array $context = []) { $this->logger->error($message, $context); }
    public function warning($message, array $context = []) { $this->logger->warning($message, $context); }
    public function notice($message, array $context = []) { $this->logger->notice($message, $context); }
    public function info($message, array $context = []) { $this->logger->info($message, $context); }
    public function debug($message, array $context = []) { $this->logger->debug($message, $context); }
    public function log($level, $message, array $context = []) { $this->logger->log($level, $message, $context); }
}