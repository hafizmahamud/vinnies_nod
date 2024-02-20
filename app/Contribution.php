<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class Contribution extends Model
{
    use LogsActivity;
    use SoftDeletes;
    use Observable;
    
    protected $fillable = [
        'donor_id',
        'paid_at',
        'quarter',
        'year',
        'amount',
    ];

    protected $casts = [
        'donor_id' => 'integer',
        'quarter'  => 'integer',
        'year'     => 'integer',
        'amount'   => 'float',
    ];

    protected static $logAttributes = [
        'paid_at',
        'quarter',
        'year',
        'amount',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    protected $dates = [
        'created_at',
        'updated_at',
        'paid_at',
    ];

    public function donor()
    {
        return $this->belongsTo('App\Donor');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'paid_at',
            'quarter',
            'year',
            'amount',
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(Contribution $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
