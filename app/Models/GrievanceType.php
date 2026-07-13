<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrievanceType extends Model
{
    protected $table = 'grievance_types';

    public $timestamps = false;

    protected $guarded = [];

    public function grievances(): HasMany
    {
        return $this->hasMany(Complain::class, 'grievance_type', 'id');
    }
}
