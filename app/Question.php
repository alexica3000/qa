<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Question extends Model
{
    use VotableTrait;

    protected $fillable = ['title', 'body'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = clean($value);
    }

    public function getUrlAttribute()
    {
        return route('questions.show', $this->slug);
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getStatusAttribute()
    {
        if($this->answers_count > 0) {
            if($this->best_answer_id) {
                return 'answered-accepted';
            }
            return 'answered';
        }

        return 'unanswered';
    }

    public function getBodyHtmlAttribute()
    {
//        return ParseDown::instance()->text($this->body);
        return clean($this->body);
    }

    public function answers()
    {
        return $this->hasMany('App\Answer')->orderBy('votes_count', 'desc');
    }

    public function acceptBestAnswer(Answer $answer)
    {
        $this->best_answer_id = $answer->id;
        $this->save();
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'question_id', 'user_id')->withTimestamps();
    }

    public function isFavorited()
    {
        return $this->favorites()->where('user_id', auth()->id())->count() > 0;
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    public function getExcerptAttribute()
    {
        return $this->excerpt(250);
    }

    private function excerpt($length)
    {
        return Str::limit(strip_tags($this->body), $length);
    }
}
