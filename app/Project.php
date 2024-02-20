<?php

namespace App;

use App\User;
use Carbon\Carbon;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use InvalidArgumentException;
use Money\Money as BaseMoney;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class Project extends Model
{
    use HasComments;
    use LogsActivity;
    use Observable;
    
    protected $fillable = [
        'name',
        'beneficiary_id',
        'overseas_conference_id',
        'overseas_project_id',
        'currency',
        'exchange_rate',
        'local_value',
        'au_value',
        'is_fully_paid',
        'is_awaiting_support',
        'comments',
        'received_at',
        'fully_paid_at',
        'completed_at',
        'estimated_completed_at',
        'updated_by',
        '_states',
        '_balance_owing',
        'status',
        'consolidated_status',
        'completion_report_received',
        'project_completion_date',
        'project_completed',
        'project_type',
    ];

    protected $casts = [
        'beneficiary_id'             => 'integer',
        'overseas_conference_id'     => 'integer',
        'is_fully_paid'              => 'boolean',
        'is_awaiting_support'        => 'boolean',
        'exchange_rate'              => 'float',
        'local_value'                => 'float',
        'au_value'                   => 'float',
        'updated_by'                 => 'integer',
        'completion_report_received' => 'integer',
        'created_at'                => 'datetime:Y-m-d H:00',
        'updated_at'                => 'datetime:Y-m-d H:00',
        'received_at'               => 'datetime:Y-m-d H:00',
        'fully_paid_at'             => 'datetime:Y-m-d H:00',
        'completed_at'              => 'datetime:Y-m-d H:00',
        'estimated_completed_at'    => 'datetime:Y-m-d H:00',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'received_at',
        'fully_paid_at',
        'completed_at',
        'estimated_completed_at',
        'project_completion_date',
    ];

    protected static $logAttributes = [
        'name',
        'beneficiary_id',
        'overseas_conference_id',
        'overseas_project_id',
        'currency',
        'exchange_rate',
        'local_value',
        'au_value',
        'is_fully_paid',
        'is_awaiting_support',
        'comments',
        'received_at',
        'fully_paid_at',
        'completed_at',
        'estimated_completed_at',
        'status',
        'consolidated_status',
        'completion_report_received',
        'project_completion_date',
        'project_type',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function beneficiary()
    {
        return $this->hasOne('App\Beneficiary', 'id', 'beneficiary_id');
    }

    public function overseasConference()
    {
        return $this->hasOne('App\OverseasConference', 'id', 'overseas_conference_id');
    }

    public function donors()
    {
        return $this->hasMany('App\Donor');
    }

    public function contributions()
    {
        return $this->hasManyThrough('App\Contribution', 'App\Donor');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function getAuValueAttribute($value)
    {
        return new Money($value);
    }

    public function getUpdatedByAttribute($value)
    {
        return User::withTrashed()->find($value);
    }

    public function getBalanceOwing()
    {
        return $this->au_value->subtract($this->getTotalPaid()->instance());
    }

    public function getTotalPaid()
    {
        return new Money(
            $this->donors->sum(function ($donor) {
                return $donor->contributions->sum('amount');
            })
        );
    }

    public function hasOverseasConference()
    {
        return !empty($this->overseasConference);
    }

    public function isFullyPaid()
    {
        return $this->getBalanceOwing()->instance()->lessThan((new Money(1))->instance());
    }

    public function updatePaymentStatus($custom_date = false, $custom_status = 'NA')
    {
        if ($custom_date) {
            $fully_paid_at = $custom_date;
        } else {
            $fully_paid_at = Carbon::now();
        }

        if (!empty($this->fully_paid_at) && $fully_paid_at >= $this->fully_paid_at) {
            $fully_paid_at = $this->fully_paid_at;
        }

        if ($custom_status != 'NA') {
            $this->is_fully_paid = (bool) $custom_status;
        }

        if ($this->isFullyPaid() || $this->is_fully_paid) {
            $this->is_fully_paid = true;
            $this->fully_paid_at = $fully_paid_at;
        } else {
            $this->is_fully_paid = false;
            $this->fully_paid_at = null;
        }

        return $this;
    }

    public function getDonorStates()
    {
        return $this->donors->map(function ($donor) {
            return $donor->localConference->state_council;
        })->map(function ($state_council) {
            return strtoupper($state_council);
        })->sort()->filter()->unique();

        // return $this->donors->map(function ($donor) {
        //     return $donor->localConference->state;
        // })->map(function ($state) {
        //     //return strtoupper($state);
        //     return Helper::getStateNameByKey($state);
        // })->sort()->filter()->unique();
    }

    public function hasDonor($donor_id)
    {
        $donors = $this->donors->reject(function ($donor) use ($donor_id) {
            return $donor->local_conference_id !== $donor_id;
        });

        return $donors->isNotEmpty();
    }

    public function updateSortFields()
    {
        $this->_states = $this->getDonorStates()->implode(' / ');
        $this->_balance_owing = $this->getBalanceOwing()->value();

        $this->save();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
        'name',
        'beneficiary_id',
        'overseas_conference_id',
        'overseas_project_id',
        'currency',
        'local_value',
        'is_fully_paid',
        'is_awaiting_support',
        'comments',
        'received_at',
        'completed_at',
        'estimated_completed_at',
        'status',
        'consolidated_status',
        'completion_report_received',
        'project_completion_date',
        'project_completed',
        ])->logOnlyDirty();
    }

    // Get attribute changes from Model for new log file
    public static function logSubject(Project $model): string
    {
        return sprintf("User [id:%d] %s/%s",
            $model->id, $model->name, $model->email
        );
    }
}
