<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingSheet extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $fillable = [
        'machine_id',
        'date',
        'description',
        'is_open'
    ];
//    protected $attributes = [
//        'code' => uniqid()
//    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function save(array $options = array())
    {
        if (empty($this->id)) {
            $this->code = strtoupper(uniqid('WS-'));
        }
        return parent::save($options);
    }

    public function machine(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Machine::class)->withTrashed();
    }

    public function working_hours(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }
}
