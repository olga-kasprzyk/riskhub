<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TraitUuid;

class Ticket extends Model
{
    use HasFactory;
    use TraitUuid;

    protected $fillable = [
      'subject',
      'content',
      'author',
      'email',
      'status'
    ];

    protected $casts = [
      'id' => 'string',
      'status' => 'boolean'
    ];
}
