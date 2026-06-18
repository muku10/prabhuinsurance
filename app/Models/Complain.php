<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $fillable = [
        'year',
        'month',
        'complain_type',
        'received_num',
        'resolved_num',
        'pending_num',
        'status',
    ];
}