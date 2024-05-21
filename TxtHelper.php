<?php

namespace App\Helper;

class TxtHelper
{
    protected $txt_stream;
    protected string $file_path;
    protected string $file_name;
    private string $path;

    public function __construct(string $file_name, string $ext = 'txt', string $path = 'php://temp')
    {
        $today = new \DateTime();

        $this->file_name = $today->format('Y-m-d-His') . '_' . $file_name . '.' . $ext;

        $this->path = $path;

        $this->file_path = $this->path . '/' . $this->file_name;

        $this->txt_stream = fopen($this->file_path, 'w+');
    }

    public function writeCsvRow(array $data): self
    {
        $string = $data[0];

        for ($i = 1; $i < count($data); ++$i) {
            $string .= ';' . $data[$i];
        }

        $this->writeln($string);

        return $this;
    }

    public function writeln(string $line): self
    {
        fwrite($this->txt_stream, $line . "\n");

        return $this;
    }

    public function writeJSON(array $json): self
    {
        fwrite($this->txt_stream, json_encode($json, JSON_PRETTY_PRINT) . "\n");

        return $this;
    }

    public function write(string $string): self
    {
        fwrite($this->txt_stream, $string);

        return $this;
    }

    public function br(int $repeat = 1): self
    {
        for ($i = 0; $i < $repeat; ++$i) {
            fwrite($this->txt_stream, "\n");
        }

        return $this;
    }

    public function hr(int $length = 10): self
    {
        $string = '';

        for ($i = 0; $i <= $length; ++$i) {
            $string .= '=';
        }

        fwrite($this->txt_stream, $string . "\n");

        return $this;
    }

    public function writeHashLn(int $length = 32): self
    {
        return $this->writeln(substr(md5(openssl_random_pseudo_bytes(20)), -$length));
    }

    public function closeFile(): void
    {
        fclose($this->txt_stream);
    }

    public function getContent()
    {
        rewind($this->txt_stream);

        return fread($this->txt_stream, fstat($this->txt_stream)['size']);
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function getFilePath(): string
    {
        return $this->file_path;
    }
}
