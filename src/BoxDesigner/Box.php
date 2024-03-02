<?php

declare(strict_types=1);

namespace BoxDesigner;

use BoxDesigner\PreBuilt\SingleLineBorder;

class Box
{
    protected string $content = '';

    /**
     * @throws SideLessThanOneException
     */
    public function __construct(private readonly int $columns = 1, private readonly int $rows = 1)
    {
        if ($columns < 1 || $rows < 1) {
            throw new SideLessThanOneException('The box rows and columns number must be greater than zero');
        }
    }

    public function setContentInsideBox(string $string): void
    {
        $string = str_replace("\r", '', $string); // remove windows specific \r carriage return ascii char
        $string = str_replace("\t", ' ', $string); // tabs will be replaced by spaces
        $this->content = $string;
    }

    private function split(string $string): array
    {
        $strLength = mb_strlen($string);
        $array = [];

        while ($strLength) {
            $array[] = mb_substr($string, 0, 1, "UTF-8");
            $string = mb_substr($string, 1, $strLength, "UTF-8");
            $strLength = mb_strlen($string);
        }

        return $array;
    }

    private function convertBoxArrayToString(array $box): string
    {
        $content = '';

        foreach ($box as $row) {
            $content .= implode('', $row) . "\n";
        }

        return $content;
    }
    public function draw(BorderInterface|null $borderProvider = null): string
    {
        if (is_null($borderProvider)) {
            $borderProvider = new SingleLineBorder();
        }

        $box = [];
        $firstRow = $borderProvider->topLeft() . str_repeat($borderProvider->horizontalLine(), $this->columns) .
            $borderProvider->topRight();

        $top = $this->split($firstRow);
        $box[] = $top;

        for ($i = 0; $i < $this->rows; $i++) {
            array_push($box, $this->split($borderProvider->verticalLine() . str_repeat(' ', $this->columns) .
                $borderProvider->verticalLine()));
        }

        $lastRow = $borderProvider->bottomLeft() . str_repeat($borderProvider->horizontalLine(), $this->columns) .
            $borderProvider->bottomRight() ;

        array_push($box, $this->split($lastRow));

        $contentPlucked = $this->split($this->content);

        for ($rowCounter = 1; $rowCounter <= $this->rows; $rowCounter++) {
            for ($columnCounter = 1; $columnCounter <= $this->columns; $columnCounter++) {
                if (count($contentPlucked) > 0) {
                    $char = array_shift($contentPlucked);
//                    echo "Col: $columnCounter Row: $rowCounter - Char: $char - Columns: {$this->columns} " . PHP_EOL;
                    if ($char === "\n") {
                        if ($columnCounter === 1) {
                            $columnCounter = 0;
                            continue;
                        }

                        $columnCounter = 0;
                        $rowCounter++;

                        continue;
                    }

                    $box[$rowCounter][$columnCounter] = $char;
                }
            }
        }

        return $this->convertBoxArrayToString($box);
    }
}
