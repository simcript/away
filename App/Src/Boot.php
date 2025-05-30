<?php

namespace Src;

final readonly class Boot
{
    private Service $service;

    public function __construct()
    {
        $request = new Request();
        $this->initService($request);
    }

    public function start(): void
    {
        $this->service->run();
    }

    private function initService(Request $request): void
    {
        $serviceName = SERVICES[$request->getSegment(0)] ?? null;
        if (is_null($serviceName)) {
            $message = "Service ({$request->getSegment(0)}) not found!";
            dieError(404, $message, 500, $message);
        }
        $service = new $serviceName($request);
        if ($service instanceof Service === false) {
            $message = "Service ($serviceName) not extended from Service abstract class";
            dieError(404, $message, 500, $message);
        }
        $this->service = $service;
    }

}