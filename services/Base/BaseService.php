<?php

namespace Services\Base;

use src\Request;
use src\Service;

final class BaseService extends Service
{
    use RoutesTrait;

    public function __construct(Request $request)
    {
        parent::__construct($request, true);
    }

    public function getDomain(): string
    {
        return getenv('BASE.BASE_URL');
    }

    public function routeMapper(string $method, string $url): string|\Closure
    {
        $routes = [
            'get:info' => fn() => $this->info(),
        ];
        return $routes["$method:$url"] ?? $url;
    }

    public function isAllowedHeader(string $header): bool
    {
        return true;
    }

    protected function preparingRequest(string $url): void
    {
        $this->client->setOption(CURLOPT_ENCODING, getenv('BASE.ENCODING'));
        $this->client->setOption(CURLOPT_TIMEOUT, 0);
        $this->client->setOption(CURLOPT_VERBOSE, 1);
        $this->client->setOption(CURLOPT_SSL_VERIFYPEER, getenv('BASE.VERIFY'));
        $this->client->setOption(CURLOPT_SSL_VERIFYHOST, getenv('BASE.VERIFY'));
        $this->client->setRedirect(getenv('BASE.MAX_REDIRECT'));

        parent::preparingRequest($url);
    }

}