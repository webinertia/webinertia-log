<?php

declare(strict_types=1);

namespace Webinertia\Log;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Log\Formatter\Json;
use Laminas\Log\Writer\Db;
use Laminas\Log\Writer\Stream;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use SplFileInfo;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Webinertia\Log\LogListener;
use Webinertia\Log\Writer\JsonDb;

class LogListenerFactory implements FactoryInterface
{
    private const LOG_FILE = __DIR__ . '/../../../../data/log/app.log';
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LogListener
    {
        $config = $container->get('config');
        $logSettings = $config['log_settings'];
        // we need the Laminas Logger instance to setup the writers
        /** @var \Laminas\Log\Logger $logger */
        $logger = $container->get(LoggerInterface::class)->getLogger();
        if (isset($config['db']) && $config['db'] !== [] && $container->has(AdapterInterface::class)) {
            $dbAdapter = $container->get(AdapterInterface::class);
            if ($dbAdapter instanceof Adapter) {
                $dbConfig = [
                    'db'    => $dbAdapter,
                    'table' => $logSettings['log_table_name'] ?? 'log',
                ];
                $dbWriter = $logSettings['log_db_entry_as_json'] ? new JsonDb($dbConfig) : new Db($dbConfig);
                $logger->addWriter($dbWriter);
            }
        } else {
            $logFileInfo = new SplFileInfo(self::LOG_FILE);
            $streamConfig = [
                'stream' => $logFileInfo->getRealPath(),
                'mode'   => 'a+',
                'chmod'  => 0644,
            ];
            $stream = new Stream($streamConfig);
            $jsonFormatter = new Json();
            $jsonFormatter->setDateTimeFormat($logSettings['log_time_format']);
            $stream->setFormatter($jsonFormatter);
            $logger->addWriter($stream);
        }
        return new $requestedName(
            $container->get(LoggerInterface::class),
            $container->get(Translator::class),
            $container->get('config')
        );
    }
}
