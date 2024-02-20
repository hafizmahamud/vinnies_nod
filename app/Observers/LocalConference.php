<?php

namespace App\Observers;

use App\LocalConference;

class LocalConference
{
    /**
     * Handle the LocalConference "created" event.
     *
     * @param  \App\LocalConference  $localConference
     * @return void
     */
    public function created(LocalConference $localConference)
    {
        //
    }

    /**
     * Handle the LocalConference "updated" event.
     *
     * @param  \App\LocalConference  $localConference
     * @return void
     */
    public function updated(LocalConference $localConference)
    {
        //
    }

    /**
     * Handle the LocalConference "deleted" event.
     *
     * @param  \App\LocalConference  $localConference
     * @return void
     */
    public function deleted(LocalConference $localConference)
    {
        //
    }

    /**
     * Handle the LocalConference "restored" event.
     *
     * @param  \App\LocalConference  $localConference
     * @return void
     */
    public function restored(LocalConference $localConference)
    {
        //
    }

    /**
     * Handle the LocalConference "force deleted" event.
     *
     * @param  \App\LocalConference  $localConference
     * @return void
     */
    public function forceDeleted(LocalConference $localConference)
    {
        //
    }
}
