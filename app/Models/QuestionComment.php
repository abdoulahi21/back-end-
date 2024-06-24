<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'user_id',
        'comment',
    ];

    protected $appends = ['time'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function question(){
        return $this->belongsTo(Question::class,'question_id');
    }
    
    public function getTimeAttribute(){
        $time = new Carbon($this->created_at);
        return $time->diffForHumans();
    }
}
