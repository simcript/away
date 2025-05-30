<?php

namespace Services\Default;

use src\Request;
use src\Service;

final class DefaultService extends Service
{
    use RoutesTrait;

    public function __construct(Request $request)
    {
        envLoader(__DIR__);
        parent::__construct($request);
    }

    public function getDomain(): string
    {
        return getenv('DEFAULT.BASE_URL');
    }

    public function routeMapper(string $method, string $url): string|\Closure
    {
        $routes = [
            'GET:info' => fn() => $this->info(),
        ];
        return $routes["$method:$url"] ?? $url;
    }

    public function isAllowedHeader(string $header): bool
    {
        return true;
    }

    protected function preparingRequest(string $url): void
    {
        $this->client->setOption(CURLOPT_ENCODING, getenv('DEFAULT.ENCODING'));
        $this->client->setOption(CURLOPT_TIMEOUT, 0);
        $this->client->setOption(CURLOPT_VERBOSE, 1);
        $this->client->setOption(CURLOPT_SSL_VERIFYPEER, getenv('DEFAULT.VERIFY'));
        $this->client->setOption(CURLOPT_SSL_VERIFYHOST, getenv('DEFAULT.VERIFY'));
        $this->client->setRedirect(getenv('DEFAULT.MAX_REDIRECT'));

        parent::preparingRequest($url);
    }

}