<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Observable;

class LocalConference extends Model
{
    use HasComments;
    use SoftDeletes;
    use LogsActivity;
    use Observable;

    protected $table = 'local_conferences';

    protected $fillable = [
        'name',
        'aggregation_number',
        'regional_council',
        'state_council',
        'diocesan_council_id',
        'parish',
        'is_flagged',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_line_1',
        'address_line_2',
        //'address_line_3',
        'suburb',
        'postcode',
        'country',
        'state_address',
        'state',
        'comments',
        'comments',
        'cost_code',
        'is_abeyant_at',
        'last_confirmed_at',
        'updated_by',
        '_diocesan_council',
    ];

    protected $casts = [
        'diocesan_council_id'   => 'integer',
        'is_flagged'            => 'boolean',
        'updated_by'            => 'integer',
        'created_at'            => 'datetime:Y-m-d H:00',
        'updated_at'            => 'datetime:Y-m-d H:00',
        'deleted_at'            => 'datetime:Y-m-d H:00',
        'is_active_at'          => 'datetime:Y-m-d H:00',
        'is_abeyant_at'         => 'datetime:Y-m-d H:00',
        'last_confirmed_at'     => 'datetime:Y-m-d H:00',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'is_active_at',
        'is_abeyant_at',
        'last_confirmed_at',
    ];

    protected static $logAttributes = [
        'name',
        'state_council',
        'regional_council',
        'diocesan_council_id',
        'parish',
        'is_flagged',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'suburb',
        'postcode',
        'state_address',
        'state',
        'comments',
        'cost_code',
        'is_active_at',
        'is_abeyant_at',
        'last_confirmed_at',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function diocesanCouncil()
    {
        return $this->hasOne('App\DiocesanCouncil', 'id', 'diocesan_council_id');
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

    public function updateSortField()
    {
        $this->_diocesan_council = $this->state;

        if ($this->diocesanCouncil) {
            $this->_diocesan_council .= '-' . $this->diocesanCouncil->name;
        }

        $this->save();
    }

    public function scopeOwn($query)
    {
        $user = Auth::user();

        if ($user->hasRole('Diocesan/Central Council User')) {
            if (!empty($user->states)) {
                $query->whereIn('local_conferences.state', $user->states);
            }

            if (!empty($user->dioceses)) {
                $query->whereIn('local_conferences.diocesan_council_id', $user->dioceses);
            }
        }

        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'name',
            'state_council',
            'regional_council',
            'diocesan_council_id',
            'parish',
            'is_flagged',
            'contact_name',
            'contact_email',
            'contact_phone',
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'suburb',
            'postcode',
            'state_address',
            'state',
            'comments',
            'cost_code',
            'is_active_at',
            'is_abeyant_at',
            'last_confirmed_at'
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(LocalConference $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
