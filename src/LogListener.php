<?php

declare(strict_types=1);

namespace Webinertia\Log;

use Exception;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\I18n\Translator;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Throwable;
use Psr\Log\LoggerInterface;
use Webinertia\Log\LogEvent;
use Webinertia\Log\LoggerAwareInterface;

use function assert;

final class LogListener implements ListenerAggregateInterface, TranslatorAwareInterface
{
    use ListenerAggregateTrait;
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /** @var LoggerInterface $logger */
    protected $logger;
    /** @var Translator $translator */
    protected $translator;

    public function __construct(
        LoggerInterface $logger,
        Translator $translator,
        protected array $config
    ) {
        $this->setLogger($logger);
        $this->translator = $translator;
        if (
            isset($config['app_setting']['log_settings'])
            && $config['app_settings']['log_settings']['log_errors']
        ) {
            $log = $this->logger->getLogger();
            $log::registerErrorHandler($log, true);
        }
    }

    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'log']);

        $sharedMananger    = $events->getSharedManager();
        // code 0
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::EMERGENCY,
            [$this, 'log'],
            $priority
        );
        // code 1
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::ALERT,
            [$this, 'log'],
            $priority
        );
        // code 2
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::CRITICAL,
            [$this, 'log'],
            $priority
        );
        // code 3
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::ERROR,
            [$this, 'log'],
            $priority
        );
        // code 4
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::WARNING,
            [$this, 'log'],
            $priority
        );
        // code 5
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::NOTICE,
            [$this, 'log'],
            $priority
        );
        // code 6
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::INFO,
            [$this, 'log'],
            $priority
        );
        // code 7
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::DEBUG,
            [$this, 'log'],
            $priority
        );
    }

    public function log(EventInterface $event)
    {
        $passContext = false;
        $name        = $event->getName();
        $logMessage  = $event->getTarget();
        $params      = $event->getParams();

        if ($event instanceof LogEvent) {
            if ($params !== []) {
                $passContext = true;
            }
            if ($passContext) {
                unset($params['translate']);
                $this->logger->$name($this->getTranslator()->translate($logMessage), $params);
            } else {
                unset($params);
                $this->logger->$name($this->getTranslator()->translate($logMessage));
            }
        }
        if ($event instanceof MvcEvent) {
            assert($event instanceof MvcEvent);
            /** @var Exception|null $ex */
            $ex = $params['exception'] ?? null;

            if ($ex instanceof Throwable || $ex instanceof Exception) {
                /**
                 * since we can only send a int code via exception classes lets switch to
                 * the standard laminas logger so we can pass a $priority
                 */
                $logger = $this->logger->getLogger();
                $logger->log(
                    $ex->getCode(),
                    $ex->getMessage(),
                    [
                        'file'  => $ex->getFile(),
                        'line'  => $ex->getLine(),
                        'trace' => $ex->getTraceAsString(),
                    ]
                );
            }
        }
    }
}
