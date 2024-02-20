<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class Comment extends Model
{
    use LogsActivity;
    use HasComments;
    use SoftDeletes;
    use Observable;

    protected $fillable = [
        'commentable_id',
        'comment',
        'is_approved',
        'user_id',
        'creted_at',
        'updated_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean'
    ];
    
    protected static $logAttributes = [
        'comment',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function commentator()
    {
        return $this->belongsTo($this->getAuthModelName(), 'user_id')->withTrashed();
    }

    public function approve()
    {
        $this->update([
            'is_approved' => true,
        ]);

        return $this;
    }
  
    public function disapprove()
    {
        $this->update([
            'is_approved' => false,
        ]);

        return $this;
    }

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->withTrashed();
    }

    protected function getAuthModelName()
    {
        if (config('comments.user_model')) {
            return config('comments.user_model');
        }

        if (!is_null(config('auth.providers.users.model'))) {
            return config('auth.providers.users.model');
        }

        throw new Exception('Could not determine the commentator model name.');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'comment',
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(Comment $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
