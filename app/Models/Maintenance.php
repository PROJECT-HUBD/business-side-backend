<?php

// app/Models/Maintenance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenance';
    public $incrementing = true;

    protected $fillable = [
        'maintain_status',
        'start_date',
        'end_date',
        'maintain_description',
    ];
  
}