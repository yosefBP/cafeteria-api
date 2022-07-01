<?php

namespace App\Listeners;

use App\Events\StockAlertNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MinimumStockProduct;
use Illuminate\Support\Facades\Log;

class SendEmailNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\StockAlertNotification  $event
     * @return void
     */
    public function handle(StockAlertNotification $event)
    {
        $usersMail = User::select('email')
                        ->whereBetween('role_id', [1, 2])
                        ->get();
        Notification::send($usersMail, new MinimumStockProduct($event->productStock));
        Log::notice('Notificación de stock enviada a los usuarios: ' . $usersMail);
    }
}
