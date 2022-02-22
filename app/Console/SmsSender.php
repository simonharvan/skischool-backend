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

class SmsSender
{

    const SENDER_NAME = 'Lyž. Medveď';
    private ISender $sender;

    public function __construct()
    {

        try {
            $settings = new CountrySenderSettings();
            $settings->add(Country::SLOVAKIA, GATE::GATE6, self::SENDER_NAME)
                ->add(Country::CZECH_REPUBLIC, GATE::GATE3, self::SENDER_NAME)
                ->add(Country::POLAND, GATE::GATE3, self::SENDER_NAME);
        } catch (InvalidGateException $e) {
            $settings = new StaticSenderSettings(Gate::GATE_TEXT_SENDER, self::SENDER_NAME);
        }

        $connection = new Connection(env('BULK_GATE_APP_ID', 0), env('BULK_GATE_APP_TOKEN', ''));

        $this->sender = new Sender($connection);
        $this->sender->setSenderSettings($settings);
    }

    public function sendMessage($phone, $text): bool
    {
        return true;
        $message = new GateMessage($phone, $text);

        $result = $this->sender->send($message);
        return $result->isSuccess();
    }
}
