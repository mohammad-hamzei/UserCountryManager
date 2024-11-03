<?php

namespace App\Repositories;

interface CountryRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCode($code);
    public function getAllCountriesWithCurrencies();
}
