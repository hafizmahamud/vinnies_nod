<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class TwinningDonation extends Model
{
    use LogsActivity;
    use SoftDeletes;
    use Observable;
    
    protected $table = 'twinning_donations';

    protected $fillable = [
        'new_remittance_id',
        'twinning_id',
        'document_id',
        'amount',
    ];

    protected $casts = [
        'new_remittance_id' => 'integer',
        'twinning_id'       => 'integer',
        'document_id'       => 'integer',
        'amount'            => 'float',
    ];
    
    protected static $logAttributes = [
        'new_remittance_id',
        'twinning_id',
        'document_id',
        'amount',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function twinning()
    {
        return $this->belongsTo('App\Twinning');
    }

    public function remittance()
    {
        return $this->belongsTo('App\NewRemittance', 'new_remittance_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'new_remittance_id',
            'twinning_id',
            'document_id',
            'amount',
        ])->logOnlyDirty();
    }

    // Get attribute changes from Model for new log file
    public static function logSubject(TwinningDonation $model): string
    {
        return sprintf("User [id:%d] %s/%s",
            $model->id, $model->name, $model->email
        );
    }
}
