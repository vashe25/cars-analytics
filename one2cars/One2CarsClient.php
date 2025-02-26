<?php

namespace one2cars;

use GuzzleHttp\Client;

class One2CarsClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://www.one2car.com',
        ]);
    }


    /**
     * /ajax/facets?
     * vehicle_type=car
     * state=
     * area=
     * type=
     * state_areas=
     * make=honda
     * model_group=
     * profile_type=
     * min_price=
     * max_price=
     * keyword=
     */
    public function getBrandData(Brand $brand): string
    {
        $params = [
            'headers' => ['x-requested-with' => 'XMLHttpRequest'],
            'query' => [
                'vehicle_type' => 'car',
                'make' => $brand->value,
            ]
        ];

        return $this->client->get('/ajax/facets', $params)->getBody()->getContents();
    }

    public function getModelData(Brand $brand, string $model): string
    {
        $params = [
            'headers' => ['x-requested-with' => 'XMLHttpRequest'],
            'query' => [
                'vehicle_type' => 'car',
                'make' => $brand->value,
                'model' => $model
            ]
        ];

        return $this->client->get('/ajax/facets', $params)->getBody()->getContents();
    }

    public function getUsedCarForSale(Brand $brand, string $model, int $page = 1): string
    {
        $params = [
            'query' => [
                'page_size' => 50,
                'page_number' => $page
            ]
        ];
        $endpoint = sprintf('/en/used-cars-for-sale/%s/%s', $brand->value, strtolower($model));
        return $this->client->get($endpoint, $params)->getBody()->getContents();
    }

}