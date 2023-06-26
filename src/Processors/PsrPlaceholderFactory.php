<?php

declare(strict_types=1);

namespace Webinertia\Log\Processors;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Webinertia\Log\Processors\PsrPlaceholder;
use Webinertia\User\Service\UserServiceInterface;

final class PsrPlaceholderFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): PsrPlaceholder {

        if ($container->has(UserServiceInterface::class)) {
            return new $requestedName($container->get(UserServiceInterface::class));
        }

        if ($container->has('UserServiceInterface')) {
            return new $requestedName($container->get('UserServiceInterface'));
        }
        return new $requestedName(null);
    }
}
