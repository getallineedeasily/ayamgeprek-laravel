<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Food;
use App\Models\Transaction;
use App\Models\User;
use Cache;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
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
     * View dashboard.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::whereBelongsTo($request->user('user'))
            ->selectRaw('invoice_id')
            ->whereNotIn('status', [TransactionStatus::CANCELLED->value, TransactionStatus::DELIVERED->value])
            ->groupBy('invoice_id', 'status')
            ->get();

        return view('users.dashboard.index', [
            'userName' => $request->user('user')->name,
            'hasActiveOrder' => collect($transactions)->isNotEmpty(),
            'activeOrderCount' => collect($transactions)->count()
        ]);
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
            $request->session()->regenerate();
            $request->session()->regenerateToken();
            return redirect()->route('user.view.home');
        } catch (Throwable $th) {
            return back()->with('error', "Ada yang salah! Silahkan coba lagi!");
        }
    }

    /**
     * View order menu
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function order(Request $request)
    {
        $key = 'foods:all:menu';
        $duration = 600;

        $foods = Cache::remember($key, $duration, function () {
            return Food::all();
        });

        return view('users.order.index', compact('foods'));
    }

    /**
     * View transactions history menu
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function history(Request $request)
    {
        $payload = $request->validate([
            'search' => ['nullable', 'ascii'],
            'status' => ['nullable', Rule::enum(TransactionStatus::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $search = $payload['search'] ?? '';
        $status = $payload['status'] ?? '';
        $start_date = $payload['start_date'] ?? '';
        $end_date = $payload['end_date'] ?? '';

        $statuses = TransactionStatus::cases();

        $transactions = Transaction::userFilteredTransactions($search, $status, $start_date, $end_date)
            ->whereBelongsTo($request->user('user'))
            ->paginate(perPage: 3)
            ->appends(['search' => $search, 'status' => $status, 'start_date' => $start_date, 'end_date' => $end_date]);

        return view(
            'users.history.index',
            compact('transactions', 'search', 'status', 'start_date', 'end_date', 'statuses')
        );
    }

    /**
     * View transaction detail
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Contracts\View\View
     */
    public function historyDetail(Request $request, Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        $transactions = Transaction::with(['food:id,name,image'])->where('invoice_id', '=', $transaction->invoice_id)
            ->where('user_id', '=', $request->user('user')->id)
            ->get();
        return view('users.history.detail', compact('transactions'));
    }

    /**
     * View payment proof
     * @param \App\Models\Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function paymentProof(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        $file = '/payment_proof/' . $transaction->payment_proof;

        try {
            if (Storage::disk('local')->exists($file)) {
                $path = Storage::disk('local')->path($file);
                return response()->file($path);
            }
            abort(404);
        } catch (Throwable $th) {
            abort(404);
        }
    }

    /**
     * Upload payment proof
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadPaymentProof(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $payload = $request->validate([
            'payment_proof' => ['required', File::image()->max('2mb')]
        ]);

        $transactions = Transaction::where('invoice_id', '=', $transaction->invoice_id)
            ->where('user_id', '=', $request->user('user')->id)
            ->where('status', '=', TransactionStatus::PENDING_PAYMENT->value)
            ->get();

        $file = $payload['payment_proof'];
        $fileName = $request->user('user')->id . '_' . Carbon::now()->timestamp . '.' . $file->extension();

        try {
            DB::transaction(function () use ($transactions, $fileName) {
                foreach ($transactions as $t) {
                    $t->payment_proof = $fileName;
                    $t->status = TransactionStatus::WAITING_CONFIRMATION->value;
                    $t->save();
                };
            });
            Storage::disk('local')->putFileAs('/payment_proof', $file, $fileName);
            return redirect()->route('user.view.history.detail', ['transaction' => $transaction->invoice_id])->with('success', 'Berhasil unggah bukti pembayaran!');
        } catch (Throwable $th) {
            return back()->with('error', "Ada yang salah! Silahkan coba lagi!");
        }
    }

    /**
     * Cancel order
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelOrder(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $transactions = Transaction::where('invoice_id', '=', $transaction->invoice_id)
            ->where('user_id', '=', $request->user('user')->id)
            ->where('status', '=', TransactionStatus::PENDING_PAYMENT->value)
            ->get();

        try {
            DB::transaction(function () use ($transactions) {
                foreach ($transactions as $t) {
                    $t->status = TransactionStatus::CANCELLED->value;
                    $t->save();
                };
            });
            return redirect()->route('user.view.history.detail', ['transaction' => $transaction->invoice_id])->with('success', 'Pesanan berhasil dibatalkan!');
        } catch (Throwable $th) {
            return back()->with('error', "Ada yang salah! Silahkan coba lagi!");
        }
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

}
