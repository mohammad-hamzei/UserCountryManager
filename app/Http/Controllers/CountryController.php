<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Repositories\CountryRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Country",
 *     description="API Endpoints of Countries"
 * )
 */
class CountryController extends Controller
{
    private $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $countries = $this->countryRepository->getAll();
        return response()->json($countries);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return response()->json(['message' => 'Display form to create a country resource']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:2|unique:countries,code',
            'currencies' => 'nullable|json',
        ]);

        $country = $this->countryRepository->create($data);
        return response()->json($country, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Country $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Country $country)
    {
        return response()->json($country);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Country $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Country $country)
    {
        return response()->json(['message' => 'Display form to edit a country resource', 'country' => $country]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Country $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:2|unique:countries,code,' . $country->id,
            'currencies' => 'nullable|json',
        ]);

        $updatedCountry = $this->countryRepository->update($country->id, $data);
        return response()->json($updatedCountry);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Country $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Country $country)
    {
        $this->countryRepository->delete($country->id);
        return response()->json(['message' => 'Country deleted successfully'], 204);
    }

    /**
     * @OA\Get(
     *     path="/api/countries-with-currencies",
     *     summary="Get all countries with currencies",
     *     tags={"Country"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of countries with their currencies",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="code", type="string", example="US"),
     *                 @OA\Property(property="name", type="string", example="United States"),
     *                 @OA\Property(
     *                     property="currencies",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="code", type="string", example="USD"),
     *                         @OA\Property(property="name", type="string", example="United States Dollar"),
     *                         @OA\Property(property="symbol", type="string", example="$")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getCountriesWithCurrencies()
    {
        return response()->json($this->countryRepository->getAllCountriesWithCurrencies());
    }
}
