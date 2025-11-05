<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }

    #[Scope]
    protected function filteredUser(Builder $query, string $search): void
    {
        $query->when($search, function (Builder $query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }, );
    }

    #[Scope]
    protected function totalCustomer(Builder $query, ?string $period = 'today', ?string $start_date = '', ?string $end_date = '')
    {
        $query->whereHas('transactions', function (Builder $query) use ($period, $start_date, $end_date) {
            $query->where('status', '=', TransactionStatus::DELIVERED->value);

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
        });

        return $query->distinct('id')->count('id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
