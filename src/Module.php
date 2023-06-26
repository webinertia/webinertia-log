<?php

declare(strict_types=1);

namespace Webinertia\Log;

final class Module
{
    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'listeners'       => $configProvider->getListenerConfig(),
            'log_processors'  => $configProvider->getLogProcessorConfig(),
            'psr_log'         => $configProvider->getPsrLogConfig(),
            'service_manager' => $configProvider->getDependencyConfig(),
        ];
    }
}