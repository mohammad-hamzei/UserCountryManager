<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('code')->index();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        $this->fetchAndStoreCountriesAndCurrencies();
    }


    private function fetchAndStoreCountriesAndCurrencies(): void
    {
        $response = Http::get('https://restcountries.com/v3.1/all?fields=name,cca2,currencies');

        if ($response->successful()) {
            $countries = $response->json();

            foreach ($countries as $country) {
                $countryId = DB::table('countries')->insertGetId([
                    'code' => $country['cca2'],
                    'name' => $country['name']['common'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($country['currencies'] as $currencyCode => $currencyData) {
                    DB::table('currencies')->insert([
                        'country_id' => $countryId,
                        'code' => $currencyCode,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }


            }
        } else {
            throw new Exception('Failed to fetch countries data from API.');
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
