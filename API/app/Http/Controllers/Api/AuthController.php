<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\customersModel;
use App\Models\sellerModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:sellers,seller_email|unique:customers,customers_email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:customer,seller' // Role is now required
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);

        if ($data['role'] === 'customer') {
            $user = customersModel::create([
                'full_name' => $data['full_name'],  
                'age' => $request->age,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'customers_email' => $data['email'],
                'password' => $data['password'],
            ]);
        } else { // role is 'seller'
            $user = sellerModel::create([
                'full_name' => $data['full_name'],
                'seller_email' => $data['email'],
                'password' => $data['password'],
                'store_name' => $request->store_name ?? $data['full_name'] . "'s Store",
                'seller_address' => $request->seller_address ?? 'Not provided',
                'phone_number' => $request->phone_number ?? 'Not provided',
            ]);
        }

        return response()->json([
            'message' => ucfirst($data['role']) . ' registered successfully!',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seller = sellerModel::where('seller_email', $request->email)->first();
        if ($seller && Hash::check($request->password, $seller->password)) {
            $token = $seller->createToken('api-token-seller')->plainTextToken;
            return response()->json([
                'message' => 'Seller logged in successfully!',
                'user_type' => 'seller',
                'token' => $token,
            ]);
        }

        $customer = customersModel::where('customers_email', $request->email)->first();
        if ($customer && Hash::check($request->password, $customer->password)) {
            $token = $customer->createToken('api-token-customer')->plainTextToken;
            return response()->json([
                'message' => 'Customer logged in successfully!',
                'user_type' => 'customer',
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out.']);
    }
} 