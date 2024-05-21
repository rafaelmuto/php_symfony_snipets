<?php

namespace App\Helper;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Tightenco\Collect\Support\Collection;

class CsvHelper
{
    private Serializer $serializer;

    public function __construct()
    {
        $objectNormalizer = new ObjectNormalizer();
        $csvEncoder = new CsvEncoder([CsvEncoder::DELIMITER_KEY => ';']);
        $this->serializer = new Serializer([$objectNormalizer], [$csvEncoder]);
    }

    public function exportCsv(mixed $data, string $filePath, string $filename, bool $addDate = true, bool $unique = true): void
    {
        $encodedCsv = $this->serializer->encode($data, 'csv');

        $uuid = '';
        if ($unique) {
            $uuid = uniqid() . '-';
        }

        $date = '';
        if ($addDate) {
            $date = (new \DateTime())->format('Y-m-d') . '-';
        }

        $uniqueFilename = "$date$uuid$filename.csv";

        $wholeFilePath = $filePath . '/' . $uniqueFilename;

        file_put_contents($wholeFilePath, $encodedCsv);
    }

    public function importCsvFromPath(string $filePath, string $delimiter = ','): Collection
    {
        $file = file_get_contents($filePath);

        return $this->importCsvFromFile($file, $delimiter);
    }

    public function importCsvFromFile(mixed $file, string $delimiter = ','): Collection
    {
        $context = [
            CsvEncoder::DELIMITER_KEY => $delimiter,
        ];

        return collect($this->serializer->decode($file, 'csv', $context));
    }

    public function encodeCSV(mixed $data)
    {
        return $this->serializer->encode($data, 'csv');
    }
}
