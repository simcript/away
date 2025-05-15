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
        $className = $this->getServiceClass($request->getServiceName());
        if (!class_exists($className)) {
            $classNameSpace = $this->getServiceClass(
                $request->getServiceName(getenv('APP.DEFAULT_SERVICE'))
            );
            if (!class_exists($classNameSpace)) {
                $message = "Service ($className) not found!";
                dieError(404, $message, 500, $message);
            } else {
                $request->service = getenv('APP.DEFAULT_SERVICE');
                $className = $classNameSpace;
            }
        }
        $class = new $className($request);
        if ($class instanceof Service === false) {
            $message = "Service ($className) not extended from Service abstract class";
            dieError(404, $message, 500, $message);
        }
        $this->service = $class;
    }

    private function getServiceClass(string $serviceName): string
    {
        return "Services\\$serviceName\\{$serviceName}Service";
    }
}