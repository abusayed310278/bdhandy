<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderDocument extends Model
{
    protected $fillable = [
        'provider_profile_id', 'document_type_id', 'document_number', 
        'document_file', 'verification_status', 'rejection_reason', 
        'verified_by', 'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
