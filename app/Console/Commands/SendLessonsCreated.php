<?php

namespace App\Console\Commands;

use App\Client;
use App\Console\SmsSender;
use App\Lesson;
use App\LessonMessage;
use App\Message;
use BulkGate\Message\Connection;
use BulkGate\Sms\Sender;
use BulkGate\Sms\Message as GateMessage;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendLessonsCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:lessonsCreated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends lessons which were just created';

    private SmsSender $sender;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->sender = new SmsSender();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        $lessons = Lesson::query()
            ->where('created_at', '<', $now->subMinutes(30))
            ->where('status', '=', Lesson::TYPE_UNPAID)
            ->get();
        Log::info('LessonsCreated:' . json_encode($lessons));
        $filteredLessons = [];
        foreach ($lessons as $lesson) {
            $from = Carbon::parse($lesson['from']);
            if (count($lesson->messages()->get()) == 0 && !$from->isCurrentDay()) {
                $filteredLessons[] = $lesson;
            }
        }
        Log::info('LessonsCreated.filtered:' . json_encode($filteredLessons));

        $clientsLessons = [];
        foreach ($filteredLessons as $lesson) {
            if (!isset($clientsLessons[$lesson['client_id']])) {
                $clientsLessons[$lesson['client_id']] = [];
            }
            $clientsLessons[$lesson['client_id']][] = $lesson;
        }
        Log::info('LessonsCreated.clientLessons:' . json_encode($filteredLessons));

        foreach ($clientsLessons as $clientId => $lessons) {
            $client = Client::find($clientId);
            $phone = $client['phone'] ?? $client['phone_2'];

            if (!isset($client) || !isset($phone)) {
                continue;
            }

            if ($this->sender->sendMessage($phone, $this->createText($lessons))) {
                $this->storeMessages($phone, $lessons);
            }
        }

        return 0;
    }

    private function storeMessages($phone, $lessons)
    {
        DB::beginTransaction();
        try {
            $result = Message::create([
                'type' => Message::TYPE_CREATED,
                'text' => $this->createText($lessons),
                'phone' => $phone,
                'created_at' => Date::now()
            ]);

            foreach ($lessons as $lesson) {
                LessonMessage::create([
                    'lesson_id' => $lesson['id'],
                    'message_id' => $result['id']
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            Message::create([
                'type' => Message::TYPE_ERROR,
                'text' => $e->getCode() . ' ' . $e->getMessage(),
                'phone' => $phone
            ]);
        }
    }

    private function createText($lessons): string
    {
        if (count($lessons) > 1) {
            $text = 'Dobrý deň, rezervovali sme pre Vás hodiny v nasledujúce dni: ';
        } else {
            $text = 'Dobrý deň, rezervovali sme pre Vás nasledujúcu hodinu: ';
        }

        $hours = [];
        foreach ($lessons as $lesson) {
            $from = Carbon::parse($lesson['from']);
            $to = Carbon::parse($lesson['to']);
            $hours[] = ucfirst($from->dayName) . $from->format(' (j.n.) H:i') . ' - ' . $to->format('H:i');
        }
        $text .= join(', ', $hours);
        $text .= ' Ak chcete urobiť zmenu, môžete tak urobiť na tel. č. +421917406403. Nájdete nás pri údolnej stanici lanovky Dedovka. Ďakujeme.';
        return $text;
    }
}
