<?php

use one2cars\Brand;
use one2cars\DataManager\DataManager;
use one2cars\One2CarsParser;
use one2cars\Printer\DataPrinter;
use Utils\DataWriter;
use one2cars\One2CarsClient;

include './vendor/autoload.php';

$client = new One2CarsClient();
$dataManager = new DataManager(new DataWriter());


if (0) { // upload data
    foreach (Brand::cases() as $brand) {
        $brandData = $client->getBrandData($brand);
        $dataManager->setBrandData($brand, $brandData);
        foreach ($brand->models() as $model) {
            $modelData = $client->getModelData($brand, $model->value);
            $dataManager->setModelData($brand, $model->value, $modelData);
        }
    }
}

if (0) { // upload selling cars
    foreach (Brand::cases() as $brand) {
        foreach ($brand->models() as $model) {
            $carList = [];
            $maxPage = 1;
            for ($page = 1; $page <= $maxPage; $page++) {
                $html = $client->getUsedCarForSale($brand, $model->value, $page);
                $parser = new One2CarsParser($html);
                $json = $parser->getJsonData();
                $data = json_decode($json, true);
                $carList = array_merge($carList, $data[1]['itemListElement']);
                if ($maxPage === 1) {
                    $maxPage = $parser->getMaxPage();
                }
            }
            $jsonCarList = json_encode($carList);
            $dataManager->setModelListData($brand, $model->value, $jsonCarList);
        }
    }
}

if (0) {
    foreach (Brand::cases() as $brand) {
        foreach ($brand->models() as $model) {
            $jsonCarList = $dataManager->getModelListData($brand, $model->value);
            $dataPrinter = new DataPrinter($jsonCarList, $brand, $model->value);

            $writer = new DataWriter();
            $writer->write(
                sprintf('%s-%s-market.html', $brand->value, $model->value),
                $dataPrinter->printHtml()
            );
        }
    }
}