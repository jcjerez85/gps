<?php

namespace Tobuli\Helpers;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Psr\Http\Message\UriInterface;

class ParsedCurl
{
    private static array $lastErrors = [];

    protected array $singleParams = [
        'compressed'
    ];

    protected array $ignoredHeaders = [
        'cookie'
    ];

    private array $tree;
    private string $curl;
    private UriInterface $uri;
    private ?string $fullUrl;
    private string $method;
    private string $body;
    private MessageBag $errors;

    /** @var string[][] */
    private array $headers;

    /** @var string[][] */
    private array $cookies;

    /** @var string[][] */
    private array $normalizedHeaders;

    public function __construct(string $curl)
    {
        $this->errors = new MessageBag();
        $curl = stripslashes($curl);
        $this->curl = $curl;
        $this->tree = $this->parseCurl($curl);
        $this->fullUrl = $this->parseUri($this->tree);
        $this->uri = new Uri($this->fullUrl);
        $this->method = $this->parseMethod($this->tree);
        $this->headers = $this->parseHeaders($this->tree);
        $this->normalizedHeaders = $this->normalizeHeaders($this->headers);
        $this->body = $this->parseBody($this->tree);
        $this->cookies = $this->parseCookies($this->tree);

        self::$lastErrors = $this->errors->all();
    }

    public function getTree(): array
    {
        return $this->tree;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        $name = strtolower($name);

        return isset($this->normalizedHeaders[$name]);
    }

    public function getHeader(string $name): array
    {
        $name = strtolower($name);

        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->normalizedHeaders[$name];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getRequestTarget(): ?string
    {
        if (isset($this->requestTarget)) {
            return $this->requestTarget;
        }

        $uri = $this->getUri();
        $path = $uri->getPath();
        $query = $uri->getQuery();

        return !empty($query) ? sprintf('%s?%s', $path, $query) : $path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getHeaderNames(): array
    {
        return array_keys($this->headers);
    }

    public function getHeaderLines(): array
    {
        $headers = $this->getHeaders();

        foreach ($headers as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }

        return $headers;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    protected function parseCurl(string $curl): array
    {
        $parts = explode(' ', $curl);
        $result = [];

        while (count($parts) > 0) {
            $chunk = trim(array_shift($parts));

            if (empty($chunk) || $chunk === 'curl') {
                continue;
            }

            if ($chunk[0] === '-') {
                $param = preg_replace('/^[-]{1,2}/', '', $chunk);
                $value = array_shift($parts);
            } else {
                $param = null;
                $value = $chunk;
            }

            while (!$this->isQuoteClosed($value)) {
                $concat = array_shift($parts);

                if (!$concat) {
                    break;
                }

                $value .= " $concat";
            }

            $value = trim($value);

            if (strlen($value) >= 2 && in_array($value[0], ["'", '"'])) {
                $value = substr($value, 1, strlen($value) - 2);
            }

            if (is_null($param) || in_array($param, $this->singleParams)) {
                $result[] = $value;
            } else {
                $result[] = [$param, $value];
            }
        }

        return $result;
    }

    protected function isQuoteClosed(?string $text): bool
    {
        $quotes = ["'", '"'];
        $text = trim($text);
        $len = strlen($text);

        if ($len < 2) {
            return true;
        }

        if (!in_array($text[0], $quotes)) {
            return true;
        }

        $quote = $text[0];

        if ($text[$len - 1] === $quote) {
            if ($text[$len - 2] === '\\') { // check for escaped character
                return false;
            }

            return true;
        }

        return false;
    }

    protected function parseUri(array $tree): ?string
    {
        $i = 1;
        $url = null;

        foreach ($tree as $arg) {
            if (!is_string($arg)) {
                continue;
            }

            $url === null
                ? $url = $arg
                : $this->errors->add('url' . ++$i, "$arg - " . trans('validation.array_max', [
                    'attribute' => 'URL',
                    'max' => 1
                ]));
        }

        $this->validate(
            ['url' => $url],
            ['url' => 'required|url'],
            ['url' => "$url - " . trans('validation.url', ['attribute' => 'URL'])],
        );

        return $url;
    }

    protected function parseMethod(array $tree): string
    {
        $tree = $this->filterTree($tree, ['X', 'data', 'data-binary']);

        foreach ($tree as $arg) {
            list($param, $value) = $arg;

            if ($param === 'X') {
                return strtoupper($value);
            } else {
                $method = 'POST';
            }
        }

        return $method ?? 'GET';
    }

    protected function parseHeaders(array $tree): array
    {
        $i = 0;
        $headers = [];
        $args = $this->filterTree($tree, ['H', 'h', 'header']);

        foreach ($args as $arg) {
            list(, $headerStr) = $arg;

            $this->validate(
                ['header' . ++$i => $headerStr],
                ['header' . $i => 'required|regex:/^[^:]+: [^:]+$/'],
                [],
                ['header' . $i => "header `$headerStr`"],
            );

            $pos = strpos($headerStr, ': ');
            $prop = trim(substr($headerStr, 0, $pos));
            $value = trim(substr($headerStr, $pos + 1));
            $normalized = strtolower($prop);

            if (in_array($normalized, $this->ignoredHeaders)) {
                continue;
            }

            $headers[$prop] = $value;
        }

        return $headers;
    }

    protected function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $name => $values) {
            $name = strtolower($name);
            $normalized[$name] = $values;
        }

        return $normalized;
    }

    protected function parseBody(array $tree)
    {
        $tree = $this->filterTree($tree, ['d', 'data', 'data-binary']);

        foreach ($tree as $arg) {
            list(, $value) = $arg;

            return $value;
        }

        return '';
    }

    protected function parseCookies(array $tree): array
    {
        $cookiePrefix = 'cookie: ';

        foreach ($this->filterTree($tree, 'H') as $arg) {
            list(, $headerStr) = $arg;
            $normalized = strtolower($headerStr);

            if (strpos($normalized, $cookiePrefix) !== 0) {
                continue;
            }

            $cookiesStr = trim(substr($headerStr, strlen($cookiePrefix)));

            break;
        }

        if (empty($cookiesStr)) {
            return [];
        }

        $cookies = [];

        foreach (explode(';', $cookiesStr) as $cookieStr) {
            $cookieStr = trim($cookieStr);
            $idx = strpos($cookieStr, '=');
            $name = substr($cookieStr, 0, $idx);
            $value = substr($cookieStr, $idx + 1);
            $cookies[$name] = $value;
        }

        return $cookies;
    }

    /**
     * @param string|array $params
     */
    protected function filterTree(array $tree, $params): array
    {
        if (is_string($params)) {
            $params = [$params];
        }

        return array_filter($tree, function ($arg) use ($params) {
            if (!is_array($arg) || count($arg) < 2) {
                return false;
            }

            list($param) = $arg;

            return in_array($param, $params);
        });
    }

    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): void
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->errors->merge($validator->errors());
        }
    }

    public function getErrors(): MessageBag
    {
        return $this->errors;
    }

    public function isValid(): bool
    {
        return $this->errors->isEmpty();
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function getCurl(): string
    {
        return $this->curl;
    }

    public static function getLastErrors(): array
    {
        return self::$lastErrors;
    }
}