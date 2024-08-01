<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
	use HasFactory, Uuids, SoftDeletes;

	protected $fillable = [
		'serie_number',
		'name',
		'brand',
		'model',
		'quantity',
		'article_type_id'
	];
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
	protected $with = ['image','technical_sheet','article_type'];

	public function suppliers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Supplier::class)
			->withTrashed()
			->withPivot('price');
	}

	public function machines(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Machine::class)->withTrashed();
	}

//    public function maintenance_sheets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(MaintenanceSheet::class);
//    }
//    public function maintenance_sheet_details(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
//    {
//        return $this->belongsToMany(MaintenanceSheetDetail::class,'maintenance_sheet_details')
//            ->withPivot('quantity','price','description');
//    }
	public function maintenance_sheet_details(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(MaintenanceSheetDetail::class);
	}

	public function article_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(ArticleType::class);
	}

	public function image(): \Illuminate\Database\Eloquent\Relations\MorphOne
	{
		return $this->morphOne(Image::class, 'imageable');
	}

	public function technical_sheet(): \Illuminate\Database\Eloquent\Relations\MorphOne
	{
		return $this->morphOne(TechnicalSheet::class, 'technical_sheetable');
	}
}
