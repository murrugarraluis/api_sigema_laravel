<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	use HasFactory, Uuids;
	protected $fillable = ["machine_id","message","date_send_notification","is_send"];
	public function machine(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
			return $this->belongsTo(Machine::class);
	}
	public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(User::class)
			->withPivot('is_view','send');
	}
}
