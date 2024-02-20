<?php

namespace App;

use Auth;
use App\Donor;
use App\Project;
use Carbon\Carbon;
use App\Contribution;
use App\Vinnies\Money;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\Observable;

class NewRemittance extends Model
{
    use HasComments;
    use LogsActivity;
    use Observable;
    
    protected $table = 'new_remittances';

    protected $fillable = [
        'state',
        'quarter',
        'year',
        'date',
        'is_approved',
        'comments',
        'projects_document_id',
        'grants_document_id',
        'councils_document_id',
        'twinnings_document_id',
        'approved_at',
        'updated_by',
        'created_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date',
        'approved_at',
    ];

    protected $casts = [
        'quarter'               => 'integer',
        'year'                  => 'integer',
        'is_approved'           => 'boolean',
        'projects_document_id'  => 'integer',
        'grants_document_id'    => 'integer',
        'councils_document_id'  => 'integer',
        'twinnings_document_id' => 'integer',
        'updated_by'            => 'integer',
        'created_by'            => 'integer',
        'created_at'            => 'datetime:Y-m-d H:00',
        'updated_at'            => 'datetime:Y-m-d H:00',
        'date'                  => 'datetime:Y-m-d H:00',
        'approved_at'           => 'datetime:Y-m-d H:00',
    ];

    protected static $logAttributes = [
        'state',
        'quarter',
        'year',
        'date',
        'is_approved',
        'comments',
        'projects_document_id',
        'grants_document_id',
        'councils_document_id',
        'twinnings_document_id',
        'approved_at',
    ];

    protected static $logOnlyDirty    = true;
    protected static $submitEmptyLogs = false;

    public function projectDocument()
    {
        return Document::find($this->projects_document_id);
    }

    public function projectDonations()
    {
        return $this->hasMany('App\ProjectDonation');
    }

    public function grantDonations()
    {
        return $this->hasMany('App\GrantDonation');
    }

    public function councilDonations()
    {
        return $this->hasMany('App\CouncilDonation');
    }

    public function twinningDonations()
    {
        return $this->hasMany('App\TwinningDonation');
    }

    public function getDonationTotal()
    {
        $data = [
            'projects'        => (new Money($this->projectDonations->sum('amount')))->value(),
            'twinning'        => (new Money($this->twinningDonations->sum('amount')))->value(),
            'grants'          => (new Money($this->grantDonations->sum('amount')))->value(),
            'councils'        => (new Money($this->councilDonations->sum('amount')))->value(),
            'projects_count'  => $this->projectDonations->count(),
            'twinnings_count' => $this->twinningDonations->count(),
            'grants_count'    => $this->grantDonations->count(),
            'councils_count'  => $this->councilDonations->count(),
        ];

        $data['total'] = $data['projects'] + $data['twinning'] + $data['grants'] + $data['councils'];
        $data['total'] = (new Money($data['total']))->value();

        return $data;
    }

    public function approve()
    {
        $this->is_approved = true;
        $this->approved_at = Carbon::now();

        $this->save();
        $this->syncDonationsToProject();
    }

    public function unapprove()
    {
        $this->is_approved = false;
        $this->approved_at = null;

        $this->save();
    }

    private function syncDonationsToProject()
    {
        $this->projectDonations->each(function ($donation) {
            $donor = Donor::firstOrCreate([
                'project_id'          => $donation->project_id,
                'local_conference_id' => $donation->local_conference_id,
            ]);

            Contribution::create([
                'donor_id' => $donor->id,
                'paid_at'  => $this->date,
                'quarter'  => $this->quarter,
                'year'     => $this->year,
                'amount'   => (new Money($donation->amount))->value(),
            ]);
        });

        // When all done, we loop once again to update project payment status
        $this->projectDonations->pluck('project_id')->unique()->each(function ($project_id) {
            $project = Project::find($project_id);

            $project->updatePaymentStatus($this->date)->save();

            $project->updated_at = Carbon::now();
            $project->updated_by = Auth::id();
            $project->save();
        });
    }

    public function getUpdatedByAttribute($value)
    {
        return User::withTrashed()->find($value);
    }

    public function getCreatedByAttribute($value)
    {
        return User::find($value);
    }

    public function scopeOwn($query)
    {
        $user = Auth::user();

        if ($user->hasRole('State User Admin') || $user->hasRole('Diocesan/Central Council User')) {
            if (!empty($user->states)) {
                $query->whereIn('state', $user->states);
            }
        }

        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
        'state',
        'quarter',
        'year',
        'date',
        'is_approved',
        'comments',
        'projects_document_id',
        'grants_document_id',
        'councils_document_id',
        'twinnings_document_id',
        'approved_at'
        ])->logOnlyDirty();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(NewRemittance $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
