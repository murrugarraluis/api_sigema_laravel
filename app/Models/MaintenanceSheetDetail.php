<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSheetDetail extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'quantity',
        'price',
        'description',
        'article_id',
        'item'
    ];

    public function maintenance_sheet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaintenanceSheet::class);
    }

    public function article(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Article::class)->withTrashed();
    }
}
