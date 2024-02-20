<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class Resources extends Model
{
    use Observable;

    protected $fillable = [
        'path',
        'url',
        'type',
        'user_id',
        'description',
    ];

    protected $casts = [
        'user_id'         => 'integer',
    ];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->withTrashed();
    }

    // Get attribute changes from Model for new log file
    public static function logSubject(Resources $model): string
    {
        return sprintf("User [id:%d] %s/%s",
            $model->id, $model->name, $model->email
        );
    }
}
