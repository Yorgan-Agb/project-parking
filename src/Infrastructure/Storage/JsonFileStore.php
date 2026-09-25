<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

final class JsonFileStore
{
    public function __construct(
        private readonly string $filePath,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function readAll(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $contents = file_get_contents($this->filePath);

        if ($contents === false || trim($contents) === '') {
            return [];
        }

        $data = json_decode($contents, true);

        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('Invalid JSON content in file "%s"', $this->filePath));
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function upsert(array $row): void
    {
        if (!isset($row['id'])) {
            throw new \InvalidArgumentException('Row must have an "id" key');
        }

        $rows = $this->readAll();
        $found = false;

        foreach ($rows as $index => $existingRow) {
            if ($existingRow['id'] === $row['id']) {
                $rows[$index] = $row;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $rows[] = $row;
        }

        $this->writeAll($rows);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function writeAll(array $rows): void
    {
        $directory = dirname($this->filePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        file_put_contents($this->filePath, $json);
    }
}
