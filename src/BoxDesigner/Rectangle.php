<?php

declare(strict_types=1);

namespace BoxDesigner;

class Rectangle
{
    protected string $content = '';

    /**
     * @throws SideLessThanOneException
     */
    public function __construct(private readonly int $rows = 1, private readonly int $columns = 1)
    {
        if ($rows < 1 || $columns < 1) {
            throw new SideLessThanOneException('The box rows and columns number must be greater than zero');
        }
    }

    public function setContentInsideBox(string $string): void
    {
        $this->content = $string;
    }

    protected function drawHorizontalLines(LineAsciiCharsInterface $lineDrawerProvider): string
    {
        $horizontalLines = '';

        for ($column = 0; $column < $this->columns; ++$column) {
            $horizontalLines .= $lineDrawerProvider->horizontalLine();
        }

        return $horizontalLines;
    }

    protected function drawTop(LineAsciiCharsInterface $lineDrawerProvider): string
    {
        $rectangleBoxTop = $lineDrawerProvider->topLeft();
        $rectangleBoxTop .= $this->drawHorizontalLines($lineDrawerProvider);
        $rectangleBoxTop .= $lineDrawerProvider->topRight();

        return $rectangleBoxTop;
    }

    protected function drawBottom(LineAsciiCharsInterface $lineDrawerProvider): string
    {
        $rectangleBoxBottom = $lineDrawerProvider->bottomLeft();
        $rectangleBoxBottom .= $this->drawHorizontalLines($lineDrawerProvider);
        $rectangleBoxBottom .= $lineDrawerProvider->bottomRight();

        return $rectangleBoxBottom;
    }

    public function draw(LineAsciiCharsInterface|null $lineDrawerProvider = null): string
    {
        if (is_null($lineDrawerProvider)) {
            $lineDrawerProvider = new SingleLineBorder();
        }

        $rectangleBox = $this->drawTop($lineDrawerProvider) . PHP_EOL;

        $sumTheBorders = 2;
        $columns = $this->columns + $sumTheBorders;

        $contentCharPosition = 0;
        $this->content = str_replace("\r", '', $this->content);
        $contentLength = strlen($this->content);

        for ($row = 0; $row < $this->rows; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                if ($column == 0 || $column == ($columns - 1)) {
                    $rectangleBox .= $lineDrawerProvider->verticalLine();
                    continue;
                }

                $add = ' ';

                if ($this->content != '' && $contentCharPosition < $contentLength) {
                    $add = $this->content[$contentCharPosition];
                    $contentCharPosition++;
                }

                $rectangleBox .= $add;
            }

            $rectangleBox .= PHP_EOL;
        }

        $rectangleBox .= $this->drawBottom($lineDrawerProvider);

        return $rectangleBox;
    }
}
