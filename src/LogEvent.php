<?php

declare(strict_types=1);

namespace Webinertia\Log;

use Laminas\EventManager\Event;
use Psr\Log\LogLevel;

final class LogEvent extends Event
{
    /** System is unusable. */
    public const EMERGENCY = LogLevel::EMERGENCY;
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     */
    public const ALERT = LogLevel::ALERT;
    /**
     * Critical conditions.
     *
     * Example: Webinertialication component unavailable, unexpected exception.
     */
    public const CRITICAL = LogLevel::CRITICAL;
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public const ERROR = LogLevel::ERROR;
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    public const WARNING = LogLevel::WARNING;
    /**
     * Normal but significant events.
     */
    public const NOTICE = LogLevel::NOTICE;
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    public const INFO = LogLevel::INFO;
    /**
     * Detailed debug information.
     *
     * Example: Input variables, SQL queries, etc.
     */
    public const DEBUG = LogLevel::DEBUG;
}
