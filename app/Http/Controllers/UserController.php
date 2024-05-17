<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function register(Request $request)
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
                auth()->login($user);
                return redirect()->route('home')->with(['success' => 'Welcome! your registration was successful.']);
            } else {
                throw new \Exception('User not saved');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Failed to register user.' . $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            $user = User::where('email', $validatedData['email'])->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors(['email' => 'User not found.']);
            }
            if (!Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
                return redirect()->back()->withInput()->withErrors(['password' => 'Invalid password.']);
            }
            Session::flash('success', 'Login successful!');
            return redirect()->route('home');
        } else {
            return view('layout.guest.login');
        }
    }


    public function dashboard()
    {
        $user = auth()->user();
        if ($user) {
            if ($user->isAdmin()) {
                $referrals = $user->referrals()->paginate(10);
            } else {
                $referrals = $user->referrals->paginate(10);
            }
        }
        return view("layout.main.home", compact('referrals'));
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
