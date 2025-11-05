<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
        $payload = $request->validate([
            'filter' => ['nullable', 'in:today,month'],
        ]);

        $filter = $payload['filter'] ?? 'today';
        $duration = 300;

        $statsKey = "dashboard:stats:{$filter}";
        $stats = Cache::remember($statsKey, $duration, function () use ($filter) {
            switch ($filter) {
                case 'month':
                    $totalRevenue = Transaction::totalRevenue('month');
                    $totalSales = Transaction::totalSales('month');
                    $mostSoldFood = Transaction::mostSoldFood('month');
                    $totalCustomer = User::totalCustomer('month');
                    break;

                default:
                    $totalRevenue = Transaction::totalRevenue('today');
                    $totalSales = Transaction::totalSales('today');
                    $mostSoldFood = Transaction::mostSoldFood('today');
                    $totalCustomer = User::totalCustomer('today');
                    break;
            }

            return compact('totalRevenue', 'totalSales', 'mostSoldFood', 'totalCustomer');
        });

        $transKey = "dashboard:transactions:{$filter}";
        $transactions = Cache::remember($transKey, $duration, function () {
            return Transaction::filteredTransactions()
                ->paginate(perPage: 3);
        });

        return view('admin.dashboard.index', [
            'name' => $request->user('admin')->name,
            'transactions' => $transactions,
            'totalSales' => $stats['totalSales'],
            'totalRevenue' => $stats['totalRevenue'],
            'mostSoldFood' => $stats['mostSoldFood'],
            'totalCustomer' => $stats['totalCustomer'],
            'filter' => $filter
        ]);
    }

    public function customer(Request $request)
    {
        $payload = $request->validate([
            'search' => ['nullable', 'ascii'],
            'page' => ['nullable', 'numeric', 'min:1', 'integer']
        ]);

        $search = $payload['search'] ?? '';
        $page = $payload['page'] ?? '1';
        $duration = 300;

        $key = "customers:search:{$search}:page:{$page}";

        $users = Cache::remember($key, $duration, function () use ($search) {
            return User::filteredUser($search)
                ->paginate(perPage: 3)
                ->appends(['search' => $search]);
        });

        return view('admin.customer.index', compact('users', 'search'));
    }

    public function resetCustomerPassword(User $user)
    {
        try {
            $user->password = $user->email;
            $user->save();
            return redirect()->route('admin.view.customer')->with('success', "Berhasil atur ulang kata sandi pelanggan {$user->name}!");
        } catch (\Throwable $th) {
            return back()->with('error', 'Ada yang salah! Silahkan coba lagi!');
        }
    }

    public function report()
    {
        return view('admin.report.index');
    }

    public function print(Request $request)
    {
        $payload = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $start_date = $payload['start_date'];
        $end_date = $payload['end_date'];

        $transactions = Transaction::filteredTransactions(start_date: $start_date, end_date: $end_date)
            ->where('status', '=', TransactionStatus::DELIVERED->value)->get();

        $totalRevenue = Transaction::totalRevenue('custom', $start_date, $end_date);
        $totalSales = Transaction::totalSales('custom', $start_date, $end_date);
        $mostSoldFood = Transaction::mostSoldFood('custom', $start_date, $end_date);
        $totalCustomer = User::totalCustomer('custom', $start_date, $end_date);

        $statuses = TransactionStatus::cases();

        $now = Carbon::now()->toDateString();

        return view('admin.report.print', [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'statuses' => $statuses,
            'transactions' => $transactions,
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'mostSoldFood' => $mostSoldFood,
            'totalCustomer' => $totalCustomer,
            'now' => $now
        ]);
    }
}
