<?php

namespace one2cars;

enum Brand: string
{
    case honda = 'honda';
    case toyota = 'toyota';
    case mazda = 'mazda';

    public function models(): iterable {
        return match ($this) {
            Brand::honda => Honda\Model::cases(),
            Brand::toyota => Toyota\Model::cases(),
            Brand::mazda => Mazda\Model::cases(),
        };
    }
}
