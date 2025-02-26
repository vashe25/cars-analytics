<?php

namespace one2cars\DataManager;

use one2cars\Brand;
use Utils\DataWriter;

class DataManager
{
    private string $postfix = 'data.json';

    public function __construct(private DataWriter $dataWriter)
    {
    }

    public function getBrandData(Brand $brand): string
    {
        return $this->dataWriter->read($this->brandFilename($brand));
    }

    public function setBrandData(Brand $brand, string $data): void
    {
        $this->dataWriter->write($this->brandFilename($brand), $data);
    }

    public function getModelData(Brand $brand, string $model): string
    {
        return $this->dataWriter->read($this->modelFilename($brand, $model));
    }

    public function setModelData(Brand $brand, string $model, string $data): void
    {
        $this->dataWriter->write($this->modelFilename($brand, $model), $data);
    }

    public function getModelListData(Brand $brand, string $model): string
    {
        return $this->dataWriter->read($this->listFilename($brand, $model));
    }

    public function setModelListData(Brand $brand, string $model, string $data): void
    {
        $this->dataWriter->write($this->listFilename($brand, $model), $data);
    }

    private function brandFilename(Brand $brand): string
    {
        return sprintf('%s-%s', $brand->value, $this->postfix);
    }

    private function modelFilename(Brand $brand, string $model): string
    {
        return sprintf('%s-%s-%s', $brand->value, $model, $this->postfix);
    }

    private function listFilename(Brand $brand, string $model): string
    {
        return sprintf('%s-%s-list-%s', $brand->value, $model, $this->postfix);
    }
}