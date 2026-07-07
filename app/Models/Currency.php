<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['symbol', 'name', 'status', 'affiliate_commission_cap'];
}
