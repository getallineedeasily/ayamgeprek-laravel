<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
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

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.view.home');
        }
        ;

        return back()->with('error', 'Ada yang salah! Silahkan coba lagi!')->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with(['user:id,name'])
            ->selectRaw('invoice_id, user_id, sum(total) as total, max(created_at) as created_at, status')
            ->groupBy(['invoice_id', 'user_id', 'status'])
            ->orderByDesc('created_at')
            ->paginate(perPage: 3);

        return view('admin.dashboard.index', ['name' => $request->user('admin')->name, 'transactions' => $transactions]);
    }

    public function customer(Request $request)
    {

        $payload = $request->validate([
            'search' => ['nullable', 'ascii'],
        ]);

        $query = $payload ? '%' . $payload['search'] . '%' : '';

        $search = $payload['search'] ?? '';

        $users = User::filteredUser($query)
            ->paginate(perPage: 3)
            ->appends(['search' => $search]);

        return view('admin.customer.index', ['users' => $users, 'search' => $search]);
    }

    public function resetCustomerPassword(User $user)
    {
        try {
            $user->password = $user->email;
            $user->save();
            return redirect()->route('admin.view.customer')->with('success', 'Berhasil atur ulang kata sandi pelanggan ' . $user->name . '!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ada yang salah! Silahkan coba lagi!');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }


    public function report()
    {
        return view('admin.report.index');
    }
}
