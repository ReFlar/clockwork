<?php

namespace Reflar\Clockwork\Controllers;

use Flarum\Http\Exception\RouteNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\TextResponse;

class ClockworkController implements RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $authenticator = app('clockwork.authenticator');
        $authenticated = $authenticator->check($request);

        if (!$authenticated) {
            return new JsonResponse([
                'message'  => app('translator')->trans('core.lib.error.permission_denied_message'),
                'requires' => $authenticator->requires(),
            ], 403);
        }

        $req = $request->getQueryParams()['request'];
        $metadata = app('clockwork')->getMetadata($req);

        if ($metadata == null) {
            throw new RouteNotFoundException();
        }

        return new JsonResponse($metadata);
    }
}
