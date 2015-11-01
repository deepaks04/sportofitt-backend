<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class MultipleSession extends Model
{

    protected $table = 'multiple_sessions';

    protected $guarded = [
        'id'
    ];
}
