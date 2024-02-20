<?php

namespace App;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class OverseasConference extends Model
{
    use HasComments;
    use LogsActivity;
    use Observable;

    protected $table = 'overseas_conferences';

    protected $fillable = [
        'id',
        'name',
        'aggregation_number',
        'central_council',
        'particular_council',
        'national_council',
        'parish',
        'is_active',
        'is_active_at',
        'is_in_status_check',
        'is_in_surrendering',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'suburb',
        'postcode',
        'state',
        'country_id',
        'twinning_status',
        'comments',
        'twinned_at',
        'untwinned_at',
        'status_check_initiated_at',
        'surrendering_initiated_at',
        'surrendering_deadline_at',
        'updated_by',
        'confirmed_date_at',
        'last_status_check_initiated',
        'final_remittance',
        'status',
        'is_abeyant_at',
        'status_check_reason',
    ];

    protected $casts = [
        'is_active'             => 'boolean',
        'is_in_status_check'    => 'boolean',
        'is_in_surrendering'    => 'boolean',
        'country_id'            => 'integer',
        'updated_by'            => 'integer',
        'created_at'            => 'datetime:Y-m-d H:00',
        'updated_at'            => 'datetime:Y-m-d H:00',
        'twinned_at'            => 'datetime:Y-m-d H:00',
        'untwinned_at'          => 'datetime:Y-m-d H:00',
        'is_active_at'          => 'datetime:Y-m-d H:00',
        'status_check_initiated_at' => 'datetime:Y-m-d H:00',
        'surrendering_initiated_at' => 'datetime:Y-m-d H:00',
        'surrendering_deadline_at'  => 'datetime:Y-m-d H:00',
        'confirmed_date_at'     => 'datetime:Y-m-d H:00',
        'is_abeyant_at'         => 'datetime:Y-m-d H:00',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'twinned_at',
        'untwinned_at',
        'is_active_at',
        'status_check_initiated_at',
        'surrendering_initiated_at',
        'surrendering_deadline_at',
        'confirmed_date_at',
        'is_abeyant_at',
    ];

    protected static $logAttributes = [
        'name',
        'central_council',
        'particular_council',
        'parish',
        'is_active',
        'is_active_at',
        'is_in_status_check',
        'is_in_surrendering',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'suburb',
        'postcode',
        'state',
        'country_id',
        'twinning_status',
        'comments',
        'twinned_at',
        'untwinned_at',
        'status_check_initiated_at',
        'surrendering_initiated_at',
        'surrendering_deadline_at',
        'confirmed_date_at',
        'last_status_check_initiated',
        'final_remittance',
        'status',
        'is_abeyant_at',
        'status_check_reason',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function country()
    {
        return $this->hasOne('App\Country', 'id', 'country_id');
    }

    public function beneficiary()
    {
        return $this->hasOne('App\Beneficiary', 'id', 'national_council');
    }

    public function twinnings()
    {
        return $this->hasMany('App\Twinning');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function getUpdatedByAttribute($value)
    {
        return User::withTrashed()->find($value);
    }

    public function isStatusCheckOverdue()
    {
        if (!$this->is_in_status_check) {
            return false;
        }

        if (!$this->status_check_initiated_at) {
            return false;
        }

        if ($this->status_check_initiated_at > Carbon::now()->subDays(90)) {
            return false;
        }

        return true;
    }

    public function isSurrenderingOverdue()
    {
        if (!$this->is_in_surrendering) {
            return false;
        }

        if (!$this->surrendering_deadline_at) {
            return false;
        }

        if ($this->surrendering_deadline_at > Carbon::now()) {
            return false;
        }

        return true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
        'name',
        'central_council',
        'particular_council',
        'parish',
        'is_active',
        'is_active_at',
        'is_in_status_check',
        'is_in_surrendering',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'suburb',
        'postcode',
        'state',
        'country_id',
        'twinning_status',
        'comments',
        'twinned_at',
        'untwinned_at',
        'status_check_initiated_at',
        'surrendering_initiated_at',
        'surrendering_deadline_at',
        'confirmed_date_at',
        'last_status_check_initiated',
        'final_remittance',
        'status',
        'is_abeyant_at',
        'status_check_reason'
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(OverseasConference $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
