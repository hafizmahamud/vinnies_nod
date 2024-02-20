<?php

namespace App;

use App\Vinnies\Money;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class OldRemittance extends Model
{
    use Observable;
    
    protected $table = 'old_remittances';

    protected $fillable = [
        'state',
        'received_at',
        'quarter',
        'year',
        'allocated',
        'payment_method',
        'cheque_number',
        'comments',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'received_at',
    ];

    protected $casts = [
        'allocated' => 'float',
        'quarter'   => 'integer',
        'year'      => 'integer',
    ];

    protected static $logAttributes = [
        'state',
        'quarter',
        'year',
        'allocated',
        'payment_method',
        'cheque_number',
        'comments',
    ];

    public function oldDonations()
    {
        return $this->hasMany('App\OldDonation');
    }

    public function getTotalDonations()
    {
        return new Money(optional($this->oldDonations)->sum('amount'));
    }

    public function getAllocated()
    {
        return new Money($this->allocated);
    }

    public function getToAllocate()
    {
        return $this->getTotalDonations()->subtract($this->getAllocated()->instance());
    }

    public function getFormattedTotalDonations()
    {
        return '$' . $this->getTotalDonations()->value();
    }

    public function getFormattedAllocated()
    {
        return '$' . $this->getAllocated()->value();
    }

    public function getFormattedToAllocate()
    {
        return '$' . $this->getToAllocate()->value();
    }

    public function getTwinningTotals()
    {
        return optional($this->oldDonations)->sum('twins');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'state',
            'quarter',
            'year',
            'allocated',
            'payment_method',
            'cheque_number',
            'comments'
        ]);
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(OldRemittance $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
