<?php

namespace App\DTO;

class UploadCSVIterator implements \Iterator
{
    private array $headers;
    private array $associative_data;
    private int $index;

    public function __construct(string $rawCsv)
    {
        $data = array_map('str_getcsv', explode("\n", $rawCsv));

        $this->headers = array_shift($data);

        foreach ($this->headers as $key => $header) {
            $this->headers[$key] = trim(strtolower($header));
        }

        $this->associative_data = [];

        foreach ($data as $row) {
            $this->associative_data[] = array_combine($this->headers, $row);
        }

        $this->index = 0;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getAssociativeArray(): array
    {
        return $this->associative_data;
    }

    public function current(): array
    {
        return $this->associative_data[$this->index];
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return isset($this->associative_data[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
