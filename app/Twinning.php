<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Twinning extends Model
{
    use HasComments;
    use LogsActivity;
    use Observable;

    protected $fillable = [
        'local_conference_id',
        'overseas_conference_id',
        'is_active',
        'type',
        'comments',
        'is_active_at',
        'is_surrendered_at',
        'twinning_period',
        'updated_by',
    ];

    protected $casts = [
        'local_conference_id'       => 'integer',
        'overseas_conference_id'    => 'integer',
        'is_active'                 => 'boolean',
        'updated_by'                => 'integer',
        'created_at'                => 'datetime:Y-m-d H:00',
        'updated_at'                => 'datetime:Y-m-d H:00',
        'is_active_at'              => 'datetime:Y-m-d H:00',
        'is_surrendered_at'         => 'datetime:Y-m-d H:00',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'is_active_at',
        'is_surrendered_at',
    ];

    protected static $logAttributes = [
        'local_conference_id',
        'overseas_conference_id',
        'is_active',
        'type',
        'comments',
        'is_active_at',
        'is_surrendered_at',
        'twinning_period',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function localConference()
    {
        return $this->hasOne('App\LocalConference', 'id', 'local_conference_id')->withTrashed();
    }

    public function overseasConference()
    {
        return $this->hasOne('App\OverseasConference', 'id', 'overseas_conference_id');
    }

    public function hasLocalConference()
    {
        return !empty($this->localConference);
    }

    public function hasOverseasConference()
    {
        return !empty($this->overseasConference);
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function getUpdatedByAttribute($value)
    {
        return User::withTrashed()->find($value);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'local_conference_id',
            'overseas_conference_id',
            'is_active',
            'type',
            'comments',
            'is_active_at',
            'is_surrendered_at',
            'twinning_period'
        ])->logOnlyDirty();
    }

    // Get attribute changes from Model for new log file
    public static function logSubject(Twinning $model): string
    {
        return sprintf("User [id:%d] %s/%s",
            $model->id, $model->name, $model->email
        );
    }
}
