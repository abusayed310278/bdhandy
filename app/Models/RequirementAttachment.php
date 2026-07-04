<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementAttachment extends Model
{
    protected $fillable = ['requirement_id', 'file', 'file_type'];

    public function requirement()
    {
        return $this->belongsTo(CustomerRequirement::class, 'requirement_id');
    }
}
