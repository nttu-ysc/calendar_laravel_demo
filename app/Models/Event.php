<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'year', 'month', 'date', 'start_time', 'end_time', 'description'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('H:i');
    }
}
