<?php

namespace Src;

use CURLFILE;
use CurlHandle;

final class Client
{
    private false|CurlHandle $client;
    private string $host;
    private string $url;
    private array $options;
    private array $headers;
    private array $queries;
    private array|string $data;
    private string $response;

    public function __construct(string $host, array $options = [])
    {
        $this->host = trim($host, '/') . '/';
        $this->options = $options;
        $this->queries = [];
        $this->headers = [];
        $this->data = [];
        $this->response = '';
        $this->url = '/';
        $this->client = curl_init();
    }

    public function send(string $method): Client
    {
        $this->prepare($method);
        $this->response = curl_exec($this->client);
        curl_close($this->client);
        return $this;
    }

    public function result(): array
    {
        // Get header size
        $headerSize = $this->getInfo(CURLINFO_HEADER_SIZE);
        return [
            'status' => $this->getInfo(CURLINFO_HTTP_CODE),
            'error' => [
                'code' => curl_errno($this->client),
                'message' => curl_error($this->client),
            ],
            'body' => substr($this->response, $headerSize),
            'headers' => $this->parseHeaders(substr($this->response, 0, $headerSize))
        ];
    }

    public function getInfo(int $option): mixed
    {
        return curl_getinfo($this->client, $option);
    }

    public function setHeader(string $key, string $value): Client
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function setUrl(string $url): Client
    {
        $this->url = trim($url, '/');
        return $this;
    }

    public function setData(string|array $data):Client
    {
        $this->data = $data;
        return $this;
    }

    public function setFile(string $field, string $filePath, string $mime = '', string $fileName = ''): Client
    {
        if(is_array($this->data)) $this->data[$field] = new CURLFILE($filePath, $mime, $fileName);
        return $this;
    }

    public function setQuery(string $key, string $value): Client
    {
        if (!empty($key)) {
            $this->queries[$key] = $value;
        }
        return $this;
    }

    public function setOption(int $option, mixed $value)
    {
        $this->options[$option] = $value;
    }

    public function setRedirect(int $max = 5, bool|string $referer = false): Client
    {
        $this->options[CURLOPT_MAXREDIRS] = $max;
        $this->options[CURLOPT_FOLLOWLOCATION] = $max > 0;
        if (!empty($referer)) {
            $this->options[CURLOPT_REFERER] = $referer;
        }
        return $this;
    }

    private function prepare(string $method): void
    {
        $this->options[CURLOPT_URL] = $this->urlGenerator();
        $this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        $this->options[CURLOPT_POSTFIELDS] = $this->data;
        $this->options[CURLOPT_HTTPHEADER] = $this->headerGenerator();
        $this->options[CURLOPT_RETURNTRANSFER] ??= 1;
        $this->options[CURLOPT_HEADER] ??= 1;
        $this->options[CURLOPT_HTTP_VERSION] ??= CURL_HTTP_VERSION_1_1;
        $this->options[CURLOPT_USERAGENT] ??= $this->headers['User-Agent'] ?? '';
        foreach ($this->options as $option => $value) {
            curl_setopt($this->client, $option, $value);
        }
    }

    private function headerGenerator(): array
    {
        $headers = [];
        $allowedHeaders = [
            'Accept', 'Access', 'Authorization', 'Cookie', 'User-Agent',
            'A-CLIENT-TOKEN', 'A-CLIENT-KEY',  'A-SERVER-HOST', 'X-API-KEY',
//            'Accept-Encoding',
            'Cache-Control', 'Connection', 'X-Forwarded-For',
        ];

        foreach ($this->headers as $key => $header) {
            if(in_array($key, $allowedHeaders)) $headers[] = "$key:$header";
        }
        return $headers;
    }

    private function parseHeaders(string $headers): array
    {
        $headerList = explode(PHP_EOL, $headers);
        $result = [];
        foreach ($headerList as $item) {
            $tmpHeader = explode(':', $item);
            $key = $tmpHeader[0];
            if (empty($key)) continue;
            unset($tmpHeader[0]);
            $value = trim(implode(':', $tmpHeader));
            $result[$key] = $value;
        }
        return $result;
    }

    private function urlGenerator(): string
    {
        $url = $this->host . $this->url;
        if (count($this->queries) > 0) {
            $url .= '?';
        }
        foreach ($this->queries as $key => $value) {
            if (empty($key)) continue;
            $url .= "$key=$value&";
        }
        return trim($url, '&');
    }

}