<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use VotableTrait;

    protected $fillable = ['body', 'user_id'];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($answer){
            $answer->question->increment('answers_count');
        });

        static::deleted(function($answer){
//            $question = $answer->question;
            $answer->question->decrement('answers_count');
//            $question->decrement('answers_count');
//            if($question->best_answer_id === $answer->id){
//                $question->best_answer_id = null;
//                $question->save();
//            }
        });
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getStatusAttribute()
    {
//        return $this->id === $this->question->best_answer_id ? 'vote-accepted' : '';
        return $this->isBest() ? 'vote-accepted' : '';
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    public function isBest()
    {
        return $this->id === $this->question->best_answer_id;
    }

    public function getBodyHtmlAttribute()
    {
        return clean($this->body);
    }
}
