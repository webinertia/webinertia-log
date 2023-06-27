<?php

declare(strict_types=1);

namespace Webinertia\Log;

use Laminas\Log\Logger;
use Psr\Log\LoggerInterface;

final class ConfigProvider
{
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                LogListener::class => LogListenerFactory::class,
            ],
        ];
    }

    public function getLogSettings(): array
    {
        return [
            'log_settings' => [
                'log_errors'      => true,
                'log_exceptions'  => true,
                'log_table_name'  => 'log',
                'log_time_format' => 'm-d-Y H:i:s',
            ],
        ];
    }

    public function getListenerConfig(): array
    {
        return [
            LogListener::class,
        ];
    }

    public function getLogProcessorConfig(): array
    {
        return [
            'aliases'   => [
                'psrplaceholder' => Processors\PsrPlaceholder::class,
            ],
            'factories' => [
                Processors\PsrPlaceholder::class => Processors\PsrPlaceholderFactory::class,
            ],
        ];
    }

    public function getPsrLogConfig(): array
    {
        return [
            LoggerInterface::class => [
                'processors' => [
                    'psrplaceholder' => [
                        'name'     => Processors\PsrPlaceholder::class,
                        'priority' => Logger::DEBUG,
                    ],
                ],
            ],
        ];
    }
}