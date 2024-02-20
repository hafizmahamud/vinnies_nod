<?php

namespace App;

use App\Vinnies\Money;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class OldDonation extends Model
{
    use Observable;

    protected $table = 'old_donations';

    protected $fillable = [
        'old_remittance_id',
        'beneficiary_id',
        'purpose',
        'myob_code',
        'state',
        'amount',
        'twins',
        'comments',
    ];

    protected $casts = [
        'old_remittance_id' => 'integer',
        'beneficiary_id'    => 'integer',
        'twins'             => 'integer',
        'amount'            => 'float',
    ];

    public function beneficiary()
    {
        return $this->hasOne('App\Beneficiary', 'id', 'beneficiary_id');
    }

    public function getFormattedAmount()
    {
        return '$' . (new Money($this->amount))->value();
    }

     // Get attribute changes from Model for new log file
     public static function logSubject(OldDonation $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
