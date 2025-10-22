<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $table = 'transactions';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id', 'id');
    }

    #[Scope]
    protected function filteredTransactions(Builder $query, ?string $search, ?string $status, ?string $start_date, ?string $end_date): void
    {
        $query->with(['user:id,name'])
            ->selectRaw('invoice_id, user_id, sum(total) as total, max(created_at) as created_at, status')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                        ->orWhere('invoice_id', 'like', $search);
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($start_date, function ($query, $start_date) {
                $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->whereDate('created_at', '<=', $end_date);
            })
            ->groupBy(['invoice_id', 'user_id', 'status'])
            ->orderByDesc('created_at');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_id',
        'user_id',
        'address',
        'food_id',
        'price',
        'quantity',
        'total',
        'payment_proof',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'quantity' => 'integer',
            'total' => 'integer',
        ];
    }
}
