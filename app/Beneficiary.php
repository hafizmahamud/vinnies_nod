<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class Beneficiary extends Model
{
    use HasComments;
    use SoftDeletes;
    use LogsActivity;
    use Observable;
    
    protected $table = 'beneficiaries';

    protected $fillable = [
        'name',
        'contact_title',
        'contact_first_name',
        'contact_last_name',
        'contact_preferred_name',
        'contact_position',
        'comments',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'suburb',
        'postcode',
        'state',
        'country_id',
        'phone',
        'fax',
        'email',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime:Y-m-d H:00',
        'updated_at' => 'datetime:Y-m-d H:00',
        'deleted_at' => 'datetime:Y-m-d H:00',
    ];

    protected static $logAttributes = [
        'name',
        'contact_title',
        'contact_first_name',
        'contact_last_name',
        'contact_preferred_name',
        'contact_position',
        'comments',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'suburb',
        'postcode',
        'state',
        'country_id',
        'phone',
        'fax',
        'email',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function country()
    {
        return $this->hasOne('App\Country', 'id', 'country_id');
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
            'name',
            'contact_title',
            'contact_first_name',
            'contact_last_name',
            'contact_preferred_name',
            'contact_position',
            'comments',
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'suburb',
            'postcode',
            'state',
            'country_id',
            'phone',
            'fax',
            'email',
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(Beneficiary $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
