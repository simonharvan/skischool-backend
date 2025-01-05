<?php


namespace App\SkiSchool\Notifications;


use App\Lesson;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class CreatedLesson extends Notification
{

    /**
     * @var Lesson
     */
    private $lesson;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        $from = Carbon::parse($this->lesson['from']);
        $to = Carbon::parse($this->lesson['to']);
        $notification = \NotificationChannels\Fcm\Resources\Notification::create()
            ->title('Hodina s ' . $this->lesson['name']. ' ('. $this->lesson['type']. ')')
            ->body(ucfirst($from->dayName) . ': ' . $from->format('H:i') . ' - ' . $to->format('H:i'));

        return FcmMessage::create()
            ->data(['data1' => 'value', 'data2' => 'value2'])
            ->notification($notification);


    }


}
