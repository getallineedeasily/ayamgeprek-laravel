<?php

namespace App\Policies;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction)
    {
        return $user->id === $transaction->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction)
    {
        return $user->id === $transaction->user_id
            && $transaction->status === TransactionStatus::PENDING_PAYMENT->value
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether admin can update the transaction
     */
    public function adminUpdate(Admin $admin, Transaction $transaction)
    {
        return $transaction->status !== TransactionStatus::PENDING_PAYMENT->value
            && $transaction->status !== TransactionStatus::CANCELLED->value
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
