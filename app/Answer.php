<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function question()
    {
        return $this->belongsTo('Question');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }
}
