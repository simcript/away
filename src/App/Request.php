<?php

namespace App;

/**
 * this class development to create a incoming request object
 * @see https://github.com/simcript/proxy
 */
final class Request
{
    public string|array $data;
    public array $files;
    public array $headers;
    public array $queries;
    public string $method;
    public string $query;
    public string $url;
    public string $service;
    public string $host;

    public function __construct()
    {
        $data = $_SERVER;
        $this->fetchMethod($data);
        $this->fetchQuery($data);
        $this->fetchHost($data);
        $this->fetchUrl($data);
        $this->fetchData();
        $this->fetchFiles();
        $this->fetchHeaders();
    }

    private function fetchData(): void
    {
        $this->data = file_get_contents('php://input');
        if(empty($this->data)) {
            $this->data = $_REQUEST;
        }
    }

    private function fetchFiles(): void
    {
        $this->files = $_FILES;
    }

    private function fetchHeaders(): void
    {
        $this->headers = getallheaders();
    }

    private function fetchMethod(array $info): void
    {
        $this->method = $info['REQUEST_METHOD'] ?? 'GET';
    }

    private function fetchHost(array $info): void
    {
        $host = ($info['REQUEST_SCHEME'] ?? 'http') . '://' . $info['HTTP_HOST'] ?? '';
        $this->host = trim($host, '/');
    }

    private function fetchUrl(array $info): void
    {
        $urlSection = explode('/', ($info['REQUEST_URI'] ?? ''));
        $this->service = $urlSection[0];
        unset($urlSection[0]);
        $path = implode('/', $urlSection);

        $path = str_replace($this->query, '', $path);
        $this->url = trim($path, '?');
    }

    private function fetchQuery(array $info): void
    {
        $this->query = $info['QUERY_STRING'] ?? '';
        $queries = explode('&', $this->query);
        $this->queries = [];
        foreach ($queries as $query) {
            $tmpQuery = explode('=', $query);
            $field = $tmpQuery[0];
            if (empty($field)) {
                continue;
            }
            unset($tmpQuery[0]);
            $value = implode('=', $tmpQuery);
            $this->queries[$field] = $value;
        }
    }


}