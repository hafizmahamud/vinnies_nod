<?php

namespace App;

use Carbon\Carbon;
use App\Traits\HasGoogle2FA;
use Spatie\Activitylog\LogOptions;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use BeyondCode\Comments\Contracts\Commentator;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Traits\Observable;

class User extends Authenticatable implements CanResetPasswordContract, Commentator
{
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use HasGoogle2FA;
    use LogsActivity;
    use CanResetPassword;
    use Observable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'states',
        'dioceses',
        'branch_display',
        'is_new',
        'has_accepted_terms',
        'has_accepted_conditions',
        'conditions_accepted_at',
        'google2fa_secret',
        'google2fa_enabled_at',
        'last_login',
        'updated_by',
        'is_active',
        'is_60_inactivity_mail_sent',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_new'                    => 'boolean',
        'has_accepted_terms'        => 'boolean',
        'has_accepted_conditions'   => 'boolean',
        'conditions_accepted_at'    => 'datetime:Y-m-d H:00',
        'google2fa_enabled_at'      => 'datetime:Y-m-d H:00',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_login',
        'conditions_accepted_at',
        'google2fa_enabled_at',
    ];

    protected static $logAttributes = [
        'first_name',
        'last_name',
        'email',
        'states',
        'branch_display',
        'is_new',
        'has_accepted_terms',
        'has_accepted_conditions',
        'conditions_accepted_at',
        'google2fa_enabled_at',
        'last_login',
        'is_active',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function getStates()
    {
        $states = $this->states;

        foreach ($states as $index => $state) {
            if ($state == 'national') {
                $states[$index] = ucwords($state);
            } else {
                $states[$index] = strtoupper($state);
            }
        }

        return implode(', ' , $states);
    }

    public function getStatesAttribute($value)
    {
        return explode('|', $value);
    }

    public function getDiocesesAttribute($value)
    {
        return explode('|', $value);
    }

    public function canEditLocalConference($local_conference)
    {
        if ($this->hasPermissionTo('update.local-conf')) {
            if ($this->hasRole('State User Admin')) {
                return in_array(strtolower($local_conference->state), $this->states);
            }

            return true;
        }

        return false;
    }

    public function canEditOverseasConference($overseas_conference)
    {
        if ($this->hasPermissionTo('update.os-conf')) {
            if ($this->hasRole('State User Admin')) {
                $states = $overseas_conference->twinnings->map(function ($twinning) {
                    return strtolower($twinning->localConference->state);
                });

                return !empty(array_intersect($this->states, $states->all()));
            }

            return true;
        }

        return false;
    }

    protected $appends = ['full_name'];

    public function getUpdatedByAttribute($value)
    {
        return User::withTrashed()->find($value);
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function deactivate()
    {
        $this->has_accepted_terms = "0";
        $this->has_accepted_conditions = "0";
        $this->conditions_accepted_at = null;
        $this->google2fa_enabled_at = null;
        $this->updated_by = Auth::id();
        $this->updated_at = Carbon::now();
        $this->is_active = 0;

        //delete password, actually just set the random password since the password is not nullable
        $this->password = bcrypt(str_random(12));
        
        $this->save();
    }

    public function needsCommentApproval($model): bool
    {
        return false;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'first_name',
            'last_name',
            'email',
            'states',
            'branch_display',
            'is_new',
            'has_accepted_terms',
            'has_accepted_conditions',
            'conditions_accepted_at',
            'google2fa_enabled_at',
            'last_login',
            'is_active',
        ])->logOnlyDirty();
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    // Get attribute changes from Model for new log file
    public static function logSubject(User $model): string
    {
        return sprintf("User [id:%d] %s/%s",
            $model->id, $model->name, $model->email
        );
    }
}
