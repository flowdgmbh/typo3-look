<?php

declare(strict_types=1);

namespace Flowd\Look\Asset;

class ContentPreviewCssCollector implements \ArrayAccess, \Iterator {

    public function __construct(private array $files = [])
    {}

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->files[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->files[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->files[] = $value;
        } else {
            $this->files[(int)$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->files[$offset]);
    }

    public function current(): mixed
    {
        return current($this->files);
    }

    public function next(): void
    {
        next($this->files);
    }

    public function key(): mixed
    {
        return key($this->files);
    }

    public function valid(): bool
    {
        return key($this->files) !== null;
    }

    public function rewind(): void
    {
        reset($this->files);
    }
}
