<?php

declare(strict_types=1);

namespace Webinertia\Log;

final class Module
{
    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'app_settings'    => $configProvider->getLogSettings(),
            'listeners'       => $configProvider->getListenerConfig(),
            'log_processors'  => $configProvider->getLogProcessorConfig(),
            'psr_log'         => $configProvider->getPsrLogConfig(),
            'service_manager' => $configProvider->getDependencyConfig(),
        ];
    }
}