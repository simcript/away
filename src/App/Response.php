<?php

namespace App;

final class Response
{
    private int $status;
    private array $headers;
    private string $body;
    private array $dictionary;

    public function __construct(int $status, array $headers, string $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function addToDictionary(string $key, string $value): void
    {
        $this->dictionary[$key] = $value;
    }

    public function emit(): void
    {
        $this->setStatus();
        $this->setHeaders();
        $this->setBody();
    }

    private function setHeaders(): void
    {
        foreach ($this->headers as $key => $value) {
            if (empty($value)) {
                header("$key", false, $this->status);
            } else {
                $value = $this->translate($value);
                header("$key: $value", false, $this->status);
            }
        }
    }

    private function setBody(): void
    {
        echo $this->translate($this->body);
    }

    private function setStatus(): void
    {
        http_response_code($this->status);
    }

    private function translate(string $text): string
    {
        foreach ($this->dictionary as $search => $replace) {
            $text = str_replace($search, $replace, $text);
        }
        return $text;
    }
}