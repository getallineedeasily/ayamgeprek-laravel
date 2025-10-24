<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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
    protected function filteredTransactions(Builder $query, string $search = null, string $status = null, string $start_date = null, string $end_date = null): void
    {
        $query->with(['user:id,name'])
            ->selectRaw('invoice_id, user_id, sum(total) as total, max(created_at) as created_at, status')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereBetween('created_at', [
                    Carbon::parse($start_date)->startOfDay(),
                    Carbon::parse($end_date)->endOfDay()
                ]);
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

    #[Scope]
    protected function totalRevenue(Builder $query, ?string $period = 'today', ?string $start_date = '', ?string $end_date = '')
    {
        match ($period) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'month' => $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year),
            'year' => $query->whereYear('created_at', Carbon::now()->year),
            'custom' => $query->whereBetween('created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay()
            ]),
            default => $query->whereDate('created_at', Carbon::today()),
        };

        return $query->where('status', TransactionStatus::DELIVERED->value)->sum('total');
    }

    #[Scope]
    protected function totalSales(Builder $query, ?string $period = 'today', ?string $start_date = '', ?string $end_date = '')
    {
        match ($period) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'month' => $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year),
            'year' => $query->whereYear('created_at', Carbon::now()->year),
            'custom' => $query->whereBetween('created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay()
            ]),
            default => $query->whereDate('created_at', Carbon::today()),
        };
        
        return $query->where('status', '=', TransactionStatus::DELIVERED->value)
            ->distinct()
            ->count('invoice_id');
    }

    #[Scope]
    protected function mostSoldFood(Builder $query, ?string $period = 'today', ?string $start_date = '', ?string $end_date = '')
    {
        match ($period) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'month' => $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year),
            'year' => $query->whereYear('created_at', Carbon::now()->year),
            'custom' => $query->whereBetween('created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay()
            ]),
            default => $query->whereDate('created_at', Carbon::today()),
        };

        $result = $query->select('food_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->with('food:id,name')
            ->groupBy('food_id')
            ->orderByDesc('total_quantity')
            ->first();

        return $result->food->name ?? "-";
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
