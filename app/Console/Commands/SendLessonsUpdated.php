<?php

namespace App\Console\Commands;

use App\Client;
use App\Console\SmsSender;
use App\Lesson;
use App\LessonChange;
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

class SendLessonsUpdated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:lessonsUpdated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends lessons which were just updated';

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
        $min30 = $now->subMinutes(30)->toDateTimeString();
        $min90 = $now->subMinutes(90)->toDateTimeString();

        $lessonChanges = LessonChange::query()
            ->where('created_at', '<', $min30)
            ->where('created_at', '>', $min90)
            ->where(function ($query) {
                $query->where('field', '=', 'from')
                    ->orWhere('field', '=', 'to');
            })
            ->get();

        $lessonChangesByLesson = [];
        foreach ($lessonChanges as $change) {
            if (!isset($lessonChangesByLesson[$change['lesson_id']])) {
                $lessonChangesByLesson[$change['lesson_id']] = [];
            }
            $lessonChangesByLesson[$change['lesson_id']][] = $change;
        }

        $clientsLessons = [];
        foreach ($lessonChangesByLesson as $lessonId => $changes) {
            $lesson = Lesson::find($lessonId);

            $lastMessage = $lesson->messages()->get()->last();

            /*
             * If there wasn't a message send before, we don't try to send
             */
            if (!isset($lastMessage)) {
                continue;
            }

            /*
             * Filter only the changes that are newer than the newest message
             */
            $filteredChanges = [];
            foreach ($changes as $change) {
                if (Carbon::parse($change['created_at'])->gt(Carbon::parse($lastMessage['created_at']))) {
                    $filteredChanges[] = $change;
                }
            }
            $from = Carbon::parse($lesson['from']);

            /*
             * If the change is for today or the change went back to the original value don't notify
             */
            if (empty($filteredChanges) || (
                    reset($filteredChanges)['old_value'] == end($filteredChanges)['new_value'] &&
                    reset($filteredChanges)['field'] == end($filteredChanges)['field']
                ) ||
                $from->isCurrentDay() ||
                $from->isPast() ||
                $lesson['status'] == Lesson::TYPE_PAID
            ) {
                continue;
            }

            if (!isset($clientsLessons[$lesson['client_id']])) {
                $clientsLessons[$lesson['client_id']] = [];
            }

            $clientsLessons[$lesson['client_id']][] = [
                'lesson' => $lesson,
                'changes' => $filteredChanges
            ];
        }

        foreach ($clientsLessons as $clientId => $lessonsWithChanges) {
            $client = Client::find($clientId);
            $phone = $client['phone'] ?? $client['phone_2'];

            /*
             * Send only messages when client has a phone
             */
            if (!isset($phone)) {
                continue;
            }

            if ($this->sender->sendMessage($phone, $this->createText($lessonsWithChanges))) {
                $this->storeMessages($phone, $lessonsWithChanges);
            }
        }

        return 0;
    }

    private function storeMessages($phone, $lessonsWithChanges)
    {
        DB::beginTransaction();
        try {
            $result = Message::create([
                'type' => Message::TYPE_UPDATED,
                'text' => $this->createText($lessonsWithChanges),
                'phone' => $phone,
                'created_at' => Date::now()
            ]);

            foreach ($lessonsWithChanges as $lessonsWithChanges) {
                LessonMessage::create([
                    'lesson_id' => $lessonsWithChanges['lesson']['id'],
                    'message_id' => $result['id']
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            Message::create([
                'type' => Message::TYPE_ERROR,
                'text' => $e->getCode() . ' ' . $e->getMessage(),
                'phone' => $phone,
                'created_at' => Date::now()
            ]);
        }
    }

    private function createText($lessonsWithChanges): string
    {
        if (count($lessonsWithChanges) == 1) {
            $text = 'Dobrý deň, vašej hodine sa zmenil čas: ';
        } else {
            $text = 'Dobrý deň, ' . count($lessonsWithChanges) .' vašim hodínám za zmenil čas: ';
        }

        $hours = [];
        foreach ($lessonsWithChanges as $lessonWithChanges) {
            $originalFrom = $lessonWithChanges['lesson']['from'];
            $originalTo = $lessonWithChanges['lesson']['to'];
            foreach (array_reverse($lessonWithChanges['changes']) as $change) {
                if ($change['field'] == 'to') {
                    $originalTo = $change['old_value'];
                }
                if ($change['field'] == 'from') {
                    $originalFrom = $change['old_value'];
                }
            }
            $originalFrom = Carbon::parse($originalFrom);
            $originalTo = Carbon::parse($originalTo);
            $from = Carbon::parse($lessonWithChanges['lesson']['from']);
            $to = Carbon::parse($lessonWithChanges['lesson']['to']);
            $hours[] = 'Z '. ucfirst($originalFrom->dayName) . $originalFrom->format(' (j.n.) H:i') . ' - ' . $originalTo->format('H:i')
            . ' na ' . ucfirst($from->dayName) . $from->format(' (j.n.) H:i') . ' - ' . $to->format('H:i') ;

        }
        $text .= join(', ', $hours);
        $text .= '. Môžete nás kontaktovať na tel. č. +421917406403. Ďakujeme, Lyž. š. U Medveďa';
        return $text;
    }
}
