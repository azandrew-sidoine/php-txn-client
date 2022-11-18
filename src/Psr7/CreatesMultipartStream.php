<?php

namespace Drewlabs\Psr7;

use Closure;
use Drewlabs\Psr7Stream\CreatesStream;
use Drewlabs\Psr7Stream\StackedStream;
use Drewlabs\Psr7Stream\Stream;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class CreatesMultipartStream implements CreatesStream
{
    /**
     * 
     * @var array<array<string,mixed>>
     */
    private $attributes;

    /**
     * 
     * @var string
     */
    private $boundary;

    public function __construct(array $attributes, string $boundary = null)
    {
        $this->boundary = $boundary ?? bin2hex(random_bytes(20));
        $this->attributes = $attributes ?? [];
    }

    /**
     * Returns the boundary of the multipart stream
     * 
     * @return string 
     */
    public function getBoundary()
    {
        return $this->boundary;
    }

    /**
     * Get the headers needed before transferring the content of a POST file
     *
     * @param array<string, string> $headers
     */
    private function getHeaders(array $headers): string
    {
        $str = '';
        foreach ($headers as $key => $value) {
            $str .= "{$key}: {$value}\r\n";
        }

        return "--{$this->boundary}\r\n" . trim($str) . "\r\n\r\n";
    }

    public function createStream()
    {
        $stack = new StackedStream();

        if (null === $this->attributes || !is_array($this->attributes)) {
            throw new \UnexpectedValueException('multipart attribute must be a valid php array');
        }

        foreach ($this->attributes as $attribute) {
            if (!is_array($attribute)) {
                throw new \UnexpectedValueException('Expect each item of the multipart data to be an associative array');
            }
            $this->addPart($stack, $attribute);
        }
        // Add the trailing boundary with CRLF
        $stack->push(Stream::new("--{$this->boundary}--\r\n"));

        return $stack;
    }


    /**
     * Add a multipart to the stacked stream
     * 
     * @param StackedStream $stream 
     * @param array $attribute 
     * @return void 
     * @throws InvalidArgumentException 
     */
    private function addPart(StackedStream $stream, array $attribute)
    {
        foreach (['contents', 'name'] as $key) {
            if (!array_key_exists($key, $attribute)) {
                throw new \InvalidArgumentException("'{$key}' is required in multipart attribute");
            }
        }
        $contents = Stream::new($attribute['contents']);
        if (empty($filename = ($attribute['filename'] ?? null))) {
            $uri = $contents->getMetadata('uri');
            if ($uri && \is_string($uri) && \substr($uri, 0, 6) !== 'php://' && \substr($uri, 0, 7) !== 'data://') {
                $filename = $uri;
            }
        }
        $this->createPart(
            $attribute['name'],
            $contents,
            $filename,
            Headers::new($attribute['headers'] ?? [])
        )(function (StreamInterface $s, $headers) use ($stream) {
            return $this->pushPart($stream, $s, $headers);
        });
    }

    /**
     * Creates a part to the multipart stream
     * 
     * @param mixed $name 
     * @param StreamInterface $stream 
     * @param string|null $filename 
     * @param Headers $headers 
     * @return Closure 
     */
    private function createPart(
        $name,
        StreamInterface $stream,
        string $filename = null,
        Headers $headers
    ) {
        return function (\Closure $callback) use ($name, $stream, $filename, $headers) {
            $disposition = $headers->get('Content-Disposition');
            if (empty($disposition)) {
                $headers['Content-Disposition'] = ($filename === '0' || $filename)
                    ? sprintf(
                        'form-data; name="%s"; filename="%s"',
                        $name,
                        basename($filename)
                    )
                    : "form-data; name=\"{$name}\"";
            }

            $length = $headers->get('Content-Length');
            if (!$length) {
                if ($length = $stream->getSize()) {
                    $headers['Content-Length'] = (string) $length;
                }
            }
            $type = $headers->get('Content-Type');
            if (!$type && ($filename === '0' || $filename)) {
                if ($type = MimeType::extToMime(pathinfo($filename, PATHINFO_EXTENSION))) {
                    $headers['Content-Type'] = $type;
                }
            }
            $callback($stream, $headers->toArray());
        };
    }

    /**
     * Push a part to the multipart stream
     * 
     * @param StackedStream $stream 
     * @param StreamInterface $body 
     * @param array $headers 
     * @return void 
     * @throws InvalidArgumentException 
     */
    private function pushPart(
        StackedStream $stream,
        StreamInterface $body,
        array $headers = []
    ) {
        $stream->push(Stream::new($this->getHeaders($headers)));
        $stream->push($body);
        $stream->push(Stream::new("\r\n"));
    }
}
