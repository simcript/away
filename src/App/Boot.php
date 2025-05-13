<?php

namespace App;

/**
 * This class development for run proxy application
 * @see https://github.com/simcript/proxy
 */
final readonly class Boot
{
    private Service $service;

    public function __construct()
    {
        try {
            $request = new Request();
            $this->initService($request, $request->service);
        } catch (\Throwable $th) {
            if (getenv('APP_DEBUG') == 'true') {
                $error = $th->getMessage() . $th->getTraceAsString();
                dieError(404, $error, 500, $error);
            }
        }
    }

    public function start(): void
    {
        $this->service->run();
    }

    private function initService(Request $request, string $service): void
    {
        $className = $this->getServiceClass($service);
        if (!class_exists($className)) {
            if (!class_exists($this->getServiceClass(getenv('APP_DEFAULT_SERVICE')))) {
                $message = "Service ($className) not found!";
                dieError(404, $message, 500, $message);
            } else {
                $className = $this->getServiceClass(getenv('APP_DEFAULT_SERVICE'));
            }
        }
        $class = new $className($request);
        if ($class instanceof Service === false) {
            $message = "Service ($className) not extended from Service abstract class";
            dieError(404, $message, 500, $message);
        }
        $this->service = $class;
    }

    private function getServiceClass(string $service): string
    {
        $serviceName = ucfirst(strtolower($service));
        return "Services\\$serviceName\\{$serviceName}Service";
    }

}