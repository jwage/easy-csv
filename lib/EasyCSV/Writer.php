<?php

declare(strict_types=1);

namespace EasyCSV;

use function array_map;
use function explode;
use function is_string;

class Writer extends AbstractBase
{
    /**
     * @param string|string[] $row
     *
     * @return false|int
     */
    public function writeRow($row)
    {
        if (is_string($row)) {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }

        return $this->getHandle()->fputcsv($row, $this->delimiter, $this->enclosure);
    }

    /**
     * @param mixed[][] $array
     */
    public function writeFromArray(array $array) : void
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }
}
