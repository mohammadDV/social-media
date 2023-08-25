<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }
}
