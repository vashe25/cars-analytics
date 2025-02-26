<?php

namespace one2cars\Printer;

use one2cars\Brand;

class DataPrinter
{
    private array $grid;
    private array $years;
    private int $maxCell;

    public function __construct(string $jsonCarList, private ?Brand $brand = null, private ?string $model = null)
    {
        $data = json_decode($jsonCarList, true);
        [
            'normalized' => $normalized,
            'minPriceRange' => $minPriceRange,
            'maxPriceRange' => $maxPriceRange
        ] = $this->normalize($data);
        $this->years = $this->getYears($normalized);
        [
            'rows' => $this->grid,
            'maxCell' => $this->maxCell
        ] = $this->makeGrid($normalized, $this->years, $minPriceRange, $maxPriceRange);
    }

    public function printConsole(): string
    {
        $str = '';
        foreach ($this->grid as $row) {
            for ($i = 0; $i < count($row); $i++) {
                if ($i > 0) {
                    if ($row[$i] > 0) {
                        $str .= sprintf(" %'. 4d", $row[$i]);
                    } else {
                        $str .= sprintf("%'. 5s", false);
                    }
                } else {
                    $str .= sprintf("฿%'. 3dk", $row[$i] * 10);
                }
            }
            $str .= PHP_EOL;
        }
        $str .= sprintf("%'. 5s", false);
        foreach ($this->years as $year) {
            $str .= sprintf(" %'.04d", $year);
        }
        $str .= PHP_EOL;
        return $str;
    }

    public function printHtml(): string
    {
        $str = '<html><head><meta charset="UTF-8"><style> table { border-spacing: 0 } th:nth-child(2) { border-bottom: 1px solid #333 } tr td:nth-child(1) { border-right: 1px solid #333 } tr:last-child td { border-top: 1px solid #333 }</style></head><body><table>';
        $str .= sprintf('<tr><th></th><th colspan="%d">%s %s</th></tr>', count($this->years), mb_strtoupper($this->brand?->value), $this->model);
        foreach ($this->grid as $row) {
            $str .= '<tr>';
            for ($i = 0; $i < count($row); $i++) {
                if ($i > 0) {
                    if ($row[$i] > 0) {
                        $str .= sprintf(
                            '<td %s>%d</td>',
                            $this->gradient($row[$i]),
                            $row[$i]
                        );
                    } else {
                        $str .= '<td></td>';
                    }
                } else {
                    $str .= sprintf("<td>฿%'. 3dk</td>", $row[$i] * 10);
                }
            }
            $str .= '</tr>';
        }
        $str .= '<tr><td></td>';
        foreach ($this->years as $year) {
            $str .= sprintf("<td>%'.04d</td>", $year);
        }
        $str .= '</tr>';
        $str .= '</table></body></html>';
        return $str;
    }

    private function getYears(array $data): array
    {
        $years = array_keys($data);
        sort($years);
        return $years;
    }

    private function makeGrid(array $data, array $years, int $minPriceRange, int $maxPriceRange): array
    {
        $maxCell = 0;
        $rows = [];
        for ($step = $maxPriceRange; $step >= $minPriceRange; $step--) {
            $row = [];
            $row[] = $step;
            foreach ($years as $year) {
                if (array_key_exists($step, $data[$year])) {
                    $row[] = $data[$year][$step];
                    if ($data[$year][$step] > $maxCell) {
                        $maxCell = $data[$year][$step];
                    }
                } else {
                    $row[] = 0;
                }
            }
            $rows[] = $row;
        }

        return compact('rows', 'maxCell');
    }

    private function normalize(array $data): array
    {
        $normalized = [];
        $minPriceRange = PHP_INT_MAX;
        $maxPriceRange = 0;
        foreach ($data as $item) {
            if (!empty($item['item']['vehicleModelDate'])) {
                if (!empty($item['item']['offers']['price'])) {
                    $price = $item['item']['offers']['price'];
                    if ($price == 0) {
                        continue;
                    }

                    $range = (int)round($price / 10000);
                    if ($range > $maxPriceRange) {
                        $maxPriceRange = $range;
                    }
                    if ($range < $minPriceRange) {
                        $minPriceRange = $range;
                    }

                    if (!array_key_exists($item['item']['vehicleModelDate'], $normalized)) {
                        $normalized[$item['item']['vehicleModelDate']][$range] = 1;
                        continue;
                    }
                    if (!array_key_exists($range, $normalized[$item['item']['vehicleModelDate']])) {
                        $normalized[$item['item']['vehicleModelDate']][$range] = 1;
                        continue;
                    }
                    $normalized[$item['item']['vehicleModelDate']][$range] += 1;
                }
            }
        }

        return compact('normalized', 'minPriceRange', 'maxPriceRange');
    }

    private function gradient(int $val): string
    {
        $step = (int) floor(255 / $this->maxCell);
        $half = (int) floor($this->maxCell / 2);
        $third = (int) floor($this->maxCell / 3);
        $red = $val > $half ? 255 : $step * $val * 2;
        $green = $val < $half ? 255 : 255 - ceil($step * $val);
        $blue = $val > $half ? 64 : 0;
        $alpha = 100;
        if ($val < $third) {
            $step = floor(100 / $third);
            $alpha = $step * $val;
        }

        return sprintf('style="background-color: rgb(%d %d %d / %d%s)"', $red, $green, $blue, $alpha, '%');
    }
}