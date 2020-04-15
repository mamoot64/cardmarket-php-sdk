<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Helper;

/**
 * Class CsvStockFileHelper
 *
 * @package Mamoot\CardMarket\Helper
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class CsvStockFileHelper
{
    private $stockFileContent;

    public function __construct(string $stockFileContent)
    {
        $this->stockFileContent = $stockFileContent;
    }

    /**
     * Store the CSV stock content into CSV file on disk.
     *
     * @param string $pathToSave
     *
     * @return bool
     */
    public function storeStockFileOnDisk(string $pathToSave): bool
    {
        if (! $csvContent = self::decodeAndUnzipContent($this->stockFileContent)) {
            throw new \RuntimeException("CSV content can't be decoded and unzip.");
        }

        return self::saveFileOnDisk($csvContent, $pathToSave);
    }

    /**
     * Save CSV file on disk.
     *
     * @param string $csvContent
     * @param string $pathToSave
     *
     * @return bool
     */
    private function saveFileOnDisk(string $csvContent, string $pathToSave): bool
    {
        return file_put_contents($pathToSave, $csvContent, LOCK_EX) ? true : false;
    }

    /**
     * Decode and Gzip the CSV stock content.
     *
     * @param string $stockFileContent
     *
     * @return string
     */
    private function decodeAndUnzipContent(string $stockFileContent): string
    {
        return gzdecode(base64_decode($stockFileContent));
    }
}
