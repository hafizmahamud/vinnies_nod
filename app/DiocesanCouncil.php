<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;

class DiocesanCouncil extends Model
{
    use Observable;

    protected $table = 'diocesan_councils';

    protected $fillable = [
        'name',
        'state',
        'is_valid',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
    ];

     // Get attribute changes from Model for new log file
     public static function logSubject(DiocesanCouncil $model): string
     {
         return sprintf("User [id:%d] %s/%s",
             $model->id, $model->name, $model->email
         );
     }
}
