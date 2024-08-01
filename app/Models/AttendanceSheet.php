<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSheet extends Model
{
	use HasFactory, Uuids, SoftDeletes;

	protected $fillable = [
		'date',
		'responsible',
		'turn',
		'is_open'
	];
	protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

	public function employees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Employee::class)
			->withTrashed()
			->withPivot('check_in', 'check_out', 'attendance', 'missed_reason', 'missed_description');
	}
}
