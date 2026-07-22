<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkPersonnel extends Model
{
    protected $table = 'network_personnel';

    protected $fillable = [
        'type',
        'fiscal_year',
        'month',
        'number',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'number' => 'integer',
        ];
    }
}
