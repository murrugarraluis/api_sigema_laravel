<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
	use HasFactory, Uuids, SoftDeletes;

	protected $fillable = [
		'document_number',
		'name',
		'lastname',
		'personal_email',
		'phone',
		'address',
		'position_id',
		'document_type_id',
		'type',
		'turn',
		'native_language'
	];
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
	protected $with = [
		'position',
		'document_type',
	];

	public function attendance_sheets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(AttendanceSheet::class)
			->withPivot('check_in', 'check_out', 'attendance', 'missed_reason', 'missed_description');
	}

	public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function position(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Position::class);
	}

	public function document_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(DocumentType::class);
	}
}
