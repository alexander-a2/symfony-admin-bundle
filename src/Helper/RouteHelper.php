<?php

namespace AlexanderA2\AdminBundle\Helper;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class RouteHelper
{
    public function __construct(
        protected RequestStack    $requestStack,
        protected RouterInterface $router,
    ) {
    }

    public function getCurrentRoute(): string
    {
        return $this->requestStack->getCurrentRequest()->get('_route');
    }

    public function getCurrentController(): string
    {
        $controllerWithAction = $this->requestStack->getCurrentRequest()->get('_controller');

        return substr($controllerWithAction, 0, strpos($controllerWithAction, '::'));
    }

    public function buildRoute(string $routeName, array $parametersMapping = [], array $parametersData = []): string
    {
        $parameters = [];

        foreach ($parametersMapping as $parameterName => $parameterKey) {
            $parameters[$parameterName] = $parametersData[$parameterKey] ?? null;
        }

        return $this->router->generate($routeName, $parameters);
    }

    public function redirectBack($addToReferer = []): RedirectResponse
    {
        $referer = $this->requestStack->getMainRequest()->headers->get('referer');

        if ($referer) {
            $targetUrl = $referer;

            if ($addToReferer) {
                $urlParts = parse_url($referer);
                parse_str($urlParts['query'] ?? '', $parameters);
                $parameters = array_merge($parameters, $addToReferer);
                $targetUrl = sprintf(
                    '%s://%s%s?%s',
                    $urlParts['scheme'],
                    $urlParts['host'],
                    $urlParts['path'],
                    http_build_query($parameters)
                );
            }

            return new RedirectResponse($targetUrl);
        }

        return new RedirectResponse('/');
    }
}
