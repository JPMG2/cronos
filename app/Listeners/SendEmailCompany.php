<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NewBranch;
use App\Mail\CompanyWelcome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

final class SendEmailCompany implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(NewBranch $event): void
    {
        Mail::to($event->company->email)->send(new CompanyWelcome($event->company));
    }
}
