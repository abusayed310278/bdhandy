<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $user->fresh()->wallet_balance >= $amount;
    }

    public function credit(User $user, float $amount, string $type, ?string $description = null, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $description, $reference) {
            $locked = User::whereKey($user->id)->lockForUpdate()->first();
            $locked->increment('wallet_balance', $amount);

            return WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => $type,
                'amount'         => $amount,
                'balance_after'  => $locked->wallet_balance,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference?->getKey(),
                'description'    => $description,
            ]);
        });
    }

    /**
     * Attempts to debit the wallet. Returns false (no changes made) if the
     * balance is insufficient, so callers can branch to another payment path.
     */
    public function debit(User $user, float $amount, string $type, ?string $description = null, ?Model $reference = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $type, $description, $reference) {
            $locked = User::whereKey($user->id)->lockForUpdate()->first();

            if ($locked->wallet_balance < $amount) {
                return false;
            }

            $locked->decrement('wallet_balance', $amount);

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => $type,
                'amount'         => -$amount,
                'balance_after'  => $locked->wallet_balance,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id'   => $reference?->getKey(),
                'description'    => $description,
            ]);

            return true;
        });
    }
}
