<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    protected $table = 'banner';
    protected $primaryKey = 'banner_id';
    protected $keyType = 'number';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'banner_title',
        'banner_img',
        'banner_description',
        'banner_link',
    ];
}
