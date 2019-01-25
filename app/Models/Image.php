<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	protected $table = 'images';

	protected $fillable = [
		'type', 'path'
	];

	public function user()
	{
		return $this->hasOne(User::class);
	}
}
