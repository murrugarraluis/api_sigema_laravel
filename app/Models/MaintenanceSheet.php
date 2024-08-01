<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceSheet extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $fillable = [
        "date",
        "responsible",
        "technical",
        "description",
        'supplier_id',
        'maintenance_type_id',
        'machine_id',
        'ref_invoice_number',
        "maximum_working_time"
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function save(array $options = array())
    {
        if (empty($this->id)) {
            $this->code = strtoupper(uniqid('MS-'));
        }
        return parent::save($options);
    }
//    public function articles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(Article::class)
//            ->withPivot('description','price','quantity');
//    }
//    public function maintenance_sheet_details(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(MaintenanceSheetDetail::class,'maintenance_sheet_details')
//            ->withPivot('description','price','quantity');
//    }
    public function maintenance_sheet_details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaintenanceSheetDetail::class);
    }

    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function maintenance_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function machine(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Machine::class)->withTrashed();
    }

    function getAmountAttribute()
    {
        return $this->maintenance_sheet_details->sum(function ($detail) {
            return ($detail->price * $detail->quantity);
        });
    }
}
