<?php

declare(strict_types=1);

namespace Webinertia\Log;

use Laminas\Db\Adapter\AdapterInterface;
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

    public function getDbAdapterConfig(): array
    {
        return [
            'driver' => 'pdo_mysql',
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
                'writers'    => [
                    'db' => [
                        'name'     => 'db',
                        'priority' => Logger::INFO,
                        'options'  => [
                            'table'     => 'log',
                            'db'        => AdapterInterface::class,
                            'formatter' => [
                                'name'    => 'db',
                                'options' => [
                                    'dateTimeFormat' => 'm-d-Y H:i:s',
                                ],
                            ],
                        ],
                    ],
                ],
                'processors' => [
                    'psrplaceholder' => [
                        'name'     => Processors\PsrPlaceholder::class,
                        'priority' => Logger::INFO,
                    ],
                ],
            ],
        ];
    }
}