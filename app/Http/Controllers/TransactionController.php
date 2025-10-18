<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Food;
use App\Models\Transaction;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            return redirect()->route('user.view.history')->with('success', 'Berhasil pesan makanan! Silahkan lakukan pembayaran!');
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
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
