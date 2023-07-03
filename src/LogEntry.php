<?php

declare(strict_types=1);

namespace Webinertia\Log;

use Laminas\Stdlib\ArrayObject;

final class LogEntry extends ArrayObject
{
    public const RESOURCE_ID = 'logs';

    /** @inheritDoc */
    public function __construct($input = [], $flags = self::ARRAY_AS_PROPS)
    {
        parent::__construct($input, $flags);
    }

    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }
}
