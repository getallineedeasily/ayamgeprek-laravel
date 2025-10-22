<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Food;
use App\Models\Transaction;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payload = $request->validate([
            'search' => ['nullable', 'ascii'],
            'status' => ['nullable', Rule::enum(TransactionStatus::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = $payload ? '%' . $payload['search'] . '%' : '';

        $search = $payload['search'] ?? '';
        $status = $payload['status'] ?? '';
        $start_date = $payload['start_date'] ?? '';
        $end_date = $payload['end_date'] ?? '';

        $transactions = Transaction::filteredTransactions($query, $status, $start_date, $end_date)
            ->paginate(3)
            ->appends(['search' => $search, 'status' => $status, 'start_date' => $start_date, 'end_date' => $end_date]);

        $statuses = TransactionStatus::cases();

        return view('admin.transaction.index', [
            'transactions' => $transactions,
            'search' => $search,
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $payload = $request->validate([
            'order' => ['required', 'array'],
            'order.*.food_id' => ['required', 'exists:foods,id'],
            'order.*.quantity' => ['nullable', 'numeric', 'min:1', 'integer'],
        ]);

        $orders = collect($payload['order'])->filter(function ($order) {
            return $order['quantity'] > 0 && $order['quantity'] !== null;
        })->pluck('quantity', 'food_id');

        $foods = Food::findMany($orders->keys());

        $invoice_id = Str::random(3) . Carbon::now()->timestamp;

        try {
            DB::transaction(function () use ($request, $orders, $foods, $invoice_id, ) {
                foreach ($foods as $food) {
                    $quantity = $orders[$food->id];
                    Transaction::create([
                        'invoice_id' => $invoice_id,
                        'user_id' => $request->user('user')->id,
                        'address' => $request->user('user')->address,
                        'food_id' => $food->id,
                        'price' => $food->price,
                        'quantity' => $quantity,
                        'total' => $food->price * $quantity,
                        'payment_proof' => null,
                        'status' => TransactionStatus::PENDING_PAYMENT,
                    ]);
                }
            });

            return redirect()->route('user.view.history.detail', ['transaction' => $invoice_id])->with('success', 'Berhasil pesan makanan! Silahkan lakukan pembayaran!');
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


    public function paymentProof(Transaction $transaction)
    {
        $path = '/payment_proof/' . $transaction->payment_proof;
        if (Storage::disk('local')->exists($path)) {
            $file = Storage::disk('local')->path($path);
            return response()->file($file);
        }

        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $transactions = Transaction::with(['food:id,name,image', 'user:id,name,email,address,phone'])
            ->where('invoice_id', '=', $transaction->invoice_id)->get();

        $statusEnum = TransactionStatus::cases();

        $statuses = collect($statusEnum)->reject(function ($status) {
            return $status->name === TransactionStatus::CANCELLED->name;
        });

        return view('admin.transaction.edit', compact('transactions', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $payload = $request->validate([
            'status' => ['required', Rule::enum(TransactionStatus::class)]
        ]);

        try {
            DB::transaction(function () use ($payload, $transaction) {
                Transaction::where('invoice_id', '=', $transaction->invoice_id)->update(['status' => $payload['status']]);
            });

            if ($payload['status'] == TransactionStatus::PENDING_PAYMENT->value) {
                $path = '/payment_proof/' . $transaction->payment_proof;

                DB::transaction(function () use ($transaction) {
                    Transaction::where('invoice_id', '=', $transaction->invoice_id)->update(['payment_proof' => null]);
                });

                if (Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->delete($path);
                }
            }
            return redirect()->route('admin.edit.txn', ['transaction' => $transaction->invoice_id])->with('success', 'Berhasil ubah status transaksi!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ada yang salah! Silahkan coba lagi!');
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
