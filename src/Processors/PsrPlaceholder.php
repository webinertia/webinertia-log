<?php

declare(strict_types=1);

namespace Webinertia\Log\Processors;

use Laminas\Log\Processor\PsrPlaceholder as Placeholder;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;

use function array_merge;

final class PsrPlaceholder extends Placeholder implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /** @var UserServiceInterface $userService */
    protected $userService;

    public function __construct(?UserServiceInterface $userService = null)
    {
        $this->userService = $userService;
    }

    public function process(array $event): array
    {
        if (! isset($event['extra']) || $event['extra'] === null) {
            $event['extra'] = [];
        }
        if ($this->userService instanceof UserServiceInterface) {
            $event['extra'] = array_merge($this->userService->getLogData(), $event['extra']);
        }
        return parent::process($event);
    }
}
