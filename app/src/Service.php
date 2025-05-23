<?php

namespace Src;

abstract class Service
{

    protected Client $client;
    protected Response $response;

    public function __construct(
        protected readonly Request $request,
        bool $loadEnv = false
    )
    {
        if ($loadEnv) {
            envLoader(ROOT_PATH . 'services' . DIRECTORY_SEPARATOR . $this->request->getServiceName());
        }

        $this->client = new Client($this->getDomain());
    }

    final public function run(): void
    {
        $route = $this->routeMapper($this->request->method, $this->request->url);
        if (is_string($route)) {
            $this->sendRequest($route);
            $this->echoResult();
        } else {
            $route();
        }
    }

    protected function preparingRequest(string $url): void
    {
        $this->client->setUrl($url)->setData($this->request->data);

        foreach ($this->request->files as $field => $file) {
            $this->client->setFile($field, $file['tmp_name'], $file['type'], $file['name']);
        }

        foreach ($this->request->headers as $header => $value) {
            if ($this->isAllowedHeader($header)) {
                $this->client->setHeader($header, $value);
            }
        }

        foreach ($this->request->queries as $key => $value) {
            $this->client->setQuery($key, $value);
        }
    }

    protected function preparingResponse(): void
    {
        $this->response->addToDictionary(
            parse_url($this->getDomain())['host'],
            parse_url($this->request->host)['host']
        );
    }

    private function sendRequest(string $url): void
    {
        $this->preparingRequest($url);

        $this->client->send($this->request->method);
    }

    private function initResponse(): Response
    {
        $result = $this->client->result();
        $headers = [];
        foreach ($result['headers'] as $key => $value) {
            if (str_contains($key, '-Encoding')) continue;
            $headers[$key] = $value;
        }
        return new Response($result['status'], $headers,
            $result['error']['code'] > 0 ? $result['error']['message'] : $result['body']
        );
    }

    private function echoResult(): void
    {
        $this->response = $this->initResponse();
        $this->preparingResponse();
        $this->response->emit();
    }

    /**
     * return domain url e.g. https://example.com
     * @return string
     */
    abstract public function getDomain(): string;

    /**
     * Returns the corresponding route of a URL
     * @param string $method
     * @param string $url
     * @return string|\Closure
     */
    abstract public function routeMapper(string $method, string $url): string|\Closure;

    /**
     * check is allowed header
     * @param string $header
     * @return bool
     */
    abstract protected function isAllowedHeader(string $header): bool;

}