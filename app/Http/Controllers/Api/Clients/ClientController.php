<?php

namespace App\Http\Controllers\Api\Clients;

use App\Client;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\UpdateClient;
use App\SkiSchool\Filters\Admin\ClientFilter;
use App\SkiSchool\Paginate\Paginate;
use App\SkiSchool\Transformers\ClientLessonTransformer;
use App\SkiSchool\Transformers\LessonTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;


class ClientController extends ApiController
{
    /**
     * ClientController constructor.
     */
    public function __construct(ClientLessonTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the clients.
     *
     * @param Client $client
     * @return JsonResponse
     */
    public function lessons(Client $client)
    {
        $lessons = $client->lessons()
            ->where('from', '>', Carbon::now())
            ->get();

        $data = [
            'lessons' => $lessons,
            'client' => $client
        ];

        $result['data'] = $this->transformer->transform($data);
        return $this->respond($result);
    }
}
