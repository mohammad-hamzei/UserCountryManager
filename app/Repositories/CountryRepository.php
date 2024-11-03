<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    public function getByCode($code)
    {
        $query = $this->model->query();
        $country = $query->select('id')->where('code', $code)->first();
        if ($country) {
            return $country->id;
        }
        return null;
    }

    public function getAllCountriesWithCurrencies()
    {
        return Country::with('currencies')->get();
    }

}
