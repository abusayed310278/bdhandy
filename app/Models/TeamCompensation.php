<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamCompensation extends Model
{
    protected $table = 'team_compensation';

    protected $fillable = [
        'team_member_id', 'effective_from', 'effective_to',
        'base_salary_monthly', 'salary_currency_id',
        'commission_type', 'commission_value', 'commission_currency_id',
        'weekly_guarantee_amount', 'payment_frequency', 'next_payout_date',
    ];
    protected $casts = ['effective_from' => 'date', 'effective_to' => 'date', 'next_payout_date' => 'date'];

    public function member()             { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function salaryCurrency()     { return $this->belongsTo(Currency::class, 'salary_currency_id'); }
    public function commissionCurrency() { return $this->belongsTo(Currency::class, 'commission_currency_id'); }
}
