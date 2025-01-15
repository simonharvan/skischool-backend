<?php

namespace App\Console;

use BulkGate\Sdk\ApiException;
use BulkGate\Sdk\Connection\ConnectionStream;
use BulkGate\Sdk\MessageSender;
use BulkGate\Sdk\Message\Sms;
use BulkGate\Sdk\Sender;
use BulkGate\Sdk\SenderException;
use BulkGate\Sdk\TypeError;
use Illuminate\Support\Facades\Log;

class SmsSender
{
    /*
     * Has to be without diacrits
     */
    private Sender $sender;

    public function __construct()
    {
        $connection = new ConnectionStream(env('BULK_GATE_APP_ID', 0), env('BULK_GATE_APP_TOKEN', ''));

        $this->sender = new MessageSender($connection);
    }

    public function sendMessage($phone, $text): bool
    {
        try {
            $message = new Sms($phone, $text);
        } catch (TypeError $e) {
            Log::info('SmsSender: type error ' . json_encode($e));
            return false;
        }

        try {
            $result = $this->sender->send($message);
        } catch (ApiException $e) {
            Log::info('SmsSender: api exception ' . json_encode($e));
            return false;
        }

        Log::info('SmsSender: ' . $result->jsonSerialize());
        return true;
    }
}
