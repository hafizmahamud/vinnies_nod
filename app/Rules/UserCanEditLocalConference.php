<?php

namespace App\Rules;

use Auth;
use Route;
use App\LocalConference;
use Illuminate\Contracts\Validation\Rule;

class UserCanEditLocalConference implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Auth::user()->canEditLocalConference(LocalConference::find($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (Route::currentRouteName() == 'twinnings.create') {
            return 'You cannot create Twinnings with Australian Conference from another State or Territory. Please review.';
        } else {
            return 'You cannot update Twinnings with Australian Conference from another State or Territory. Please review.';
        }
    }
}
