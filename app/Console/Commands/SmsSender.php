<?php

namespace App\Console\Commands;

use BulkGate\Message\Connection;
use BulkGate\Sms\Message as GateMessage;
use BulkGate\Sms\Sender;

class SmsSender
{

    private Sender $sender;

    public function __construct()
    {
        $connection = new Connection(env('BULK_GATE_APP_ID', ''), env('BULK_GATE_APP_TOKEN', ''));

        $this->sender = new Sender($connection);
    }

    public function sendMessage($phone, $text): bool
    {
        return true;
        $message = new GateMessage($phone, $text);

        $result = $this->sender->send($message);
        return $result->isSuccess();
    }
}
