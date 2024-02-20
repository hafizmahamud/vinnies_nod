<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class Donor extends Model
{
    use LogsActivity;
    use SoftDeletes;
    use Observable;

    protected $fillable = [
        'local_conference_id',
        'project_id',
    ];

    protected $casts = [
        'local_conference_id' => 'integer',
        'project_id'          => 'integer',
    ];
    
    protected static $logAttributes = [
        'local_conference_id',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function contributions()
    {
        return $this->hasMany('App\Contribution');
    }

    public function localConference()
    {
        return $this->HasOne('App\LocalConference', 'id', 'local_conference_id')->withTrashed();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'local_conference_id',
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(Donor $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
