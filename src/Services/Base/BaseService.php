<?php

namespace Services\Base;

use App\Service;

final class BaseService extends Service
{

    public function getDomain(): string
    {
        return 'https://base.service.url'; // Enter base service url
    }

    public function getRoute(string $url): string
    {
        return $url;
    }

    public function isAllowedHeader(string $header): bool
    {
        return true;
    }

}