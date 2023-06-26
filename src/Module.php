<?php

declare(strict_types=1);

namespace Webinertia\Log;

final class Module
{
    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'app_settings'    => [], // this key is here to prevent errors when running stand alone
            'db'              => $configProvider->getDbAdapterConfig(),
            'listeners'       => $configProvider->getListenerConfig(),
            'log_processors'  => $configProvider->getLogProcessorConfig(),
            'psr_log'         => $configProvider->getPsrLogConfig(),
            'service_manager' => $configProvider->getDependencyConfig(),
        ];
    }
}