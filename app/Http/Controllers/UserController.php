<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\CountryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Country;

/**
 * @OA\Tag(
 *     name="User",
 *     description="API Endpoints of Users"
 * )
 */
class UserController extends Controller
{
    private $userRepository;
    private $countryRepository;

    public function __construct(UserRepositoryInterface $userRepository, CountryRepositoryInterface $countryRepository)
    {
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"User"},
     *     summary="User - Get all users with filtering and sorting",
     *     description="Retrieve a list of all users, with optional filters for country and currency, and sorting options.",
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter by country name"
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter by currency code"
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "email", "country", "currency"}),
     *         description="Field to sort by (e.g., name, email, country, or currency)"
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc"),
     *         description="Sort order (ascending or descending)"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="mohammad"),
     *                 @OA\Property(property="email", type="string", example="mohammad.hamzei1990@gmail.com"),
     *                 @OA\Property(property="country", type="string", example="South Georgia"),
     *                 @OA\Property(property="currency", type="string", example="SHP")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->only(['country', 'currency']);
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $users = $this->userRepository->getAllUsers($filters, $sortBy, $sortOrder)
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'country' => $user->country_name,
                    'currency' => $user->currency_code,
                ];
            });

        return response()->json($users);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"User"},
     *     summary="User - Create a new user",
     *     description="Add a new user with a specific country and currency",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "country_id", "currency_id"},
     *             @OA\Property(property="name", type="string", example="mohammad"),
     *             @OA\Property(property="email", type="string", format="email", example="mohammad.hamzei1990@gmail.com"),
     *             @OA\Property(property="country_id", type="integer", example=1),
     *             @OA\Property(property="currency_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or currency does not belong to the country"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'country_id' => 'required|exists:countries,id',
            'currency_id' => 'required|exists:currencies,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $country = Country::with('currencies')->find($request->country_id);
        if (!$country->currencies->contains('id', $request->currency_id)) {
            return response()->json(['error' => 'The selected currency does not belong to the specified country.'], 400);
        }

        $user = $this->userRepository->create($request->all());
        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="User - Get user by ID",
     *     description="Retrieve user information by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show($id)
    {
        $user = $this->userRepository->getById($id);

        // در صورت عدم یافتن کاربر، پیغام خطا برگرداند
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // ساختار داده کاربر با نام کشور و کد ارز
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'country' => $user->country ? $user->country->name : null,
            'currency' => $user->currency ? $user->currency->code : null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        return response()->json($userData);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="User - Update user information",
     *     description="Update user data by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Name"),
     *             @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
     *             @OA\Property(property="country_id", type="integer", example=1),
     *             @OA\Property(property="currency_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'country_id' => 'required|exists:countries,id',
            'currency_id' => 'required|exists:currencies,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = $this->userRepository->update($id, $request->all());
        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="User - Delete user",
     *     description="Delete a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->userRepository->delete($id);
        return response()->json(null, 204);
    }
}
