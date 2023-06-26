<?php

declare(strict_types=1);

namespace Webinertia\Log\Processors;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Webinertia\Log\Processors\PsrPlaceholder;

final class PsrPlaceholderFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): PsrPlaceholder {
        return new $requestedName($container->get(UserServiceInterface::class));
    }
}
