<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class ProjectDonation extends Model
{
    use LogsActivity;
    use SoftDeletes;
    use Observable;
    protected $table = 'project_donations';

    protected $fillable = [
        'new_remittance_id',
        'project_id',
        'local_conference_id',
        'document_id',
        'amount',
    ];

    protected $casts = [
        'new_remittance_id'   => 'integer',
        'project_id'          => 'integer',
        'local_conference_id' => 'integer',
        'document_id'         => 'integer',
        'amount'              => 'float',
    ];
    
    protected static $logAttributes = [
        'new_remittance_id',
        'project_id',
        'local_conference_id',
        'document_id',
        'amount',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function donor()
    {
        return $this->belongsTo('App\LocalConference', 'local_conference_id', 'id')->withTrashed();
    }

    public function remittance()
    {
        return $this->belongsTo('App\NewRemittance', 'new_remittance_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'local_conference_id',
        ])->logOnlyDirty();
    }

    // Get attribute changes from Model for new log file
    public static function logSubject(ProjectDonation $model): string
    {
        return sprintf("User [id:%d] %s/%s",
            $model->id, $model->name, $model->email
        );
    }
}
