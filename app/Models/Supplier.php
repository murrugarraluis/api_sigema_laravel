<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
	use HasFactory, Uuids, SoftDeletes;

	protected $fillable = [
		'document_number',
		'name',
		'phone',
		'email',
		'address',
		'supplier_type_id',
		'document_type_id',
	];
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
	protected $with = ['supplier_type','document_type'];

	public function banks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Bank::class)
			->withPivot('account_number', 'interbank_account_number');
	}

	public function articles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Article::class)->withTrashed();
	}

	public function maintenance_sheets(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(MaintenanceSheet::class);
	}

	public function supplier_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(SupplierType::class);
	}

	public function document_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(DocumentType::class);
	}
}
