<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalSheet extends Model
{
    use HasFactory;
    protected $fillable = ['path'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function technical_sheetable()
    {
        return $this->morphTo();
    }
}
