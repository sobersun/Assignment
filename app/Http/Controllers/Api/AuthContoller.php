<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthContoller extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'referral_code' => 'nullable|string|exists:users,referral_code|max:10',
            ]);

            $user = new User();
            $user->name = ucwords($validatedData['name']);
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->referral_code = Str::random(6); // generate unique referral code
            $user->state_id = User::STATE_ACTIVE;
            $user->role_id = User::ROLE_USER;
            if ($request->has('referral_code')) {
                $referredBy = User::where('referral_code', $request->input('referral_code'))->first();
                if ($referredBy) {
                    $user->referred_by = $referredBy->id;
                }
            }

            DB::beginTransaction();
            if ($user->save()) {
                $commission = new Commission();
                $commission->user_id = $user->id;
                $commission->initial_amount = Commission::INITIAL_AMOUNT;
                $commission->amount = Commission::INITIAL_AMOUNT;
                if ($user->referred_by) {
                    $commissionEarned = $user->commission->amount  *  Commission::COMMISSION_RATE;
                    $user->commission->update([
                        'amount' => $user->commission->amount + $commissionEarned,
                        'commission_percentage' => Commission::DIRECT_COMMISSION_PERCENTAGE,
                        'total_referrals' => $user->commission->total_referrals + 1
                    ]);
                }
                $commission->save();
                DB::commit();
                return response()->json(['success' => $user], 201);
            } else {
                throw new \Exception('User not saved');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Failed to register user.' . $e->getMessage()], 400);
        }
    }
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            $user = User::where('email', $validatedData['email'])->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.'
                ], 401);
            }
            if (!Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
                return response()->json([
                    'message' => 'Invalid password.'
                ], 401);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token
            ], 200);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Failed to register user.' . $e->getMessage()], 400);
        }
    }
    public function referrals(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $referrals = $user->referrals()->paginate(10);
                $totalEarnings = $user->getTotalEarning();
                $referrals = new LengthAwarePaginator($referrals->items(), $referrals->total(), $referrals->perPage(), $referrals->currentPage());
                return response()->json([
                    'totalEarnings' => $totalEarnings,
                    'referrals' => $referrals,
                    'current_page' => $referrals->currentPage(),
                    'last_page' => $referrals->lastPage(),
                    'per_page' => $referrals->perPage(),
                    'total' => $referrals->total(),
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 401);
            }
        } catch (\Exception $e) {

            return response()->json(['error' => 'Error' . $e->getMessage()], 400);
        }
    }

    public function earning()
    {
        try {
            $user = Auth::user();
            if ($user) {
                $earning = ['earning' => $user->getTotalEarning(), 'total_referrals' => $user->earning->total_referrals];
                return response()->json([
                    'earning' => $earning
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error' . $e->getMessage()], 400);
        }
    }
}
