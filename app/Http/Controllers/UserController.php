<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Throwable;

class UserController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('user')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('user.view.home');
        }
        ;

        return back()->with('error', 'Ada yang salah! Silahkan coba lagi!')->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('users.dashboard.index', ['userName' => $request->user('user')->name]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'ascii'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'numeric'],
            'password' => ['required', 'min:8', 'max:30'],
            'address' => ['required', 'ascii'],
        ]);

        try {
            $user = User::create($payload);
            Auth::guard('user')->login($user);
            return redirect()->route('user.view.home');
        } catch (Throwable $th) {
            return back()->with('error', "Ada yang salah! Silahkan coba lagi!");
        }
    }

    public function order(Request $request)
    {
        $foods = Food::all();
        return view('users.order.index', compact('foods'));
    }

    public function history(Request $request)
    {
        $transaction = Transaction::whereBelongsTo($request->user('user'))
            ->selectRaw('invoice_id, sum(total) as total, max(created_at) as created_at, status')
            ->groupBy(['invoice_id', 'user_id', 'address', 'status'])
            ->orderByDesc('created_at')
            ->paginate(perPage: 3);

        return view('users.history.index', compact('transaction'));
    }

    public function historyDetail(Request $request, Transaction $transaction)
    {
        $transactions = Transaction::with(['food:id,name'])->where('invoice_id', '=', $transaction->invoice_id)
            ->where('user_id', '=', $request->user('user')->id)
            ->get();
        return view('users.history.detail', compact('transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $user = $request->user('user');
        return view('users.profile.index', [
            'name' => $user->name,
            'phone' => $user->phone,
            'address' => $user->address,
            'email' => $user->email
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->input('old_password') && $request->input('password')) {
            $payload = $request->validate([
                'name' => ['required', 'ascii'],
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($request->user('user')->id)],
                'phone' => ['required', 'numeric'],
                'address' => ['required', 'ascii'],
                'old_password' => ['required', 'current_password:user'],
                'password' => ['required', 'min:8', 'max:30'],
            ]);
        } else {
            $payload = $request->validate([
                'name' => ['required', 'ascii'],
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($request->user('user')->id)],
                'phone' => ['required', 'numeric'],
                'address' => ['required', 'ascii'],
            ]);
        }

        $user = User::find($request->user('user')->id);

        try {
            $user->update($payload);
            $user->save();
            return redirect()->route('user.view.profile')->with('success', "Berhasil ubah profil!");
        } catch (Throwable $th) {
            return back()->with('error', "Ada yang salah! Silahkan coba lagi!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
