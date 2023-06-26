<?php

/**
 * Logger aware trait
 *
 * @method LoggerInterface emergency($message, array $context = array())
 * @method LoggerInterface alert($message, array $context = array())
 * @method LoggerInterface critical($message, array $context = array())
 * @method LoggerInterface error($message, array $context = array())
 * @method LoggerInterface warning($message, array $context = array())
 * @method LoggerInterface notice($message, array $context = array())
 * @method LoggerInterface info($message, array $context = array())
 * @method LoggerInterface debug($message, array $context = array())
 */

declare(strict_types=1);

namespace Webinertia\Log;

use Laminas\Log\LoggerAwareTrait as LmLoggerAwareTrait;
use Psr\Log\LoggerAwareTrait as PsrLoggerAwareTrait;
use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    use LmLoggerAwareTrait, PsrLoggerAwareTrait {
        PsrLoggerAwareTrait::setLogger insteadof LmLoggerAwareTrait;
    }

    /** @var LoggerInterface $logger */
    protected $logger;

    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
