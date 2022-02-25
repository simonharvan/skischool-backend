<?php

namespace App\Console;

use BulkGate\Message\Connection;
use BulkGate\Sms\Country;
use BulkGate\Sms\ISender;
use BulkGate\Sms\Message as GateMessage;
use BulkGate\Sms\Sender;
use BulkGate\Sms\SenderSettings\CountrySenderSettings;
use BulkGate\Sms\SenderSettings\Gate;
use BulkGate\Sms\SenderSettings\InvalidGateException;
use BulkGate\Sms\SenderSettings\StaticSenderSettings;
use Illuminate\Support\Facades\Log;

class SmsSender
{
    /*
     * Has to be without diacrits
     */
    private ISender $sender;

    public function __construct()
    {
        $sender_name = env('SMS_SENDER_NAME', 'SMS');
        try {
            $settings = new CountrySenderSettings();
            $settings->add(Country::SLOVAKIA, GATE::GATE6, $sender_name)
                ->add(Country::CZECH_REPUBLIC, GATE::GATE3, $sender_name)
                ->add(Country::POLAND, GATE::GATE3, $sender_name);
        } catch (InvalidGateException $e) {
            $settings = new StaticSenderSettings(Gate::GATE_TEXT_SENDER, $sender_name);
        }

        $connection = new Connection(env('BULK_GATE_APP_ID', 0), env('BULK_GATE_APP_TOKEN', ''));

        $this->sender = new Sender($connection);
        $this->sender->setSenderSettings($settings);
    }

    public function sendMessage($phone, $text): bool
    {
        $message = new GateMessage($phone, $text);

        $result = $this->sender->send($message);

        Log::info('SmsSender: ' . json_encode($message));

        return $result->isSuccess();
    }
}
