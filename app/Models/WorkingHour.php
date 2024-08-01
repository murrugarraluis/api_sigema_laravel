<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHour extends Model
{
    use HasFactory,Uuids,SoftDeletes;
    protected $fillable = [
        'date_time_start',
        'date_time_end'
    ];
    protected $hidden = ['created_at', 'updated_at','deleted_at'];
    public function working_sheet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WorkingSheet::class);
    }
}
