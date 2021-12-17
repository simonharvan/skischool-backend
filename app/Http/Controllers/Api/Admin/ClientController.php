<?php

namespace App\Http\Controllers\Api\Admin;

use App\Client;
use App\Http\Controllers\Api\ApiController;
use App\SkiSchool\Filters\Admin\ClientFilter;
use App\SkiSchool\Paginate\Paginate;
use App\Skischool\Transformers\ClientTransformer;
use Illuminate\Http\JsonResponse;


class ClientController extends ApiController
{
    /**
     * InstructorController constructor.
     *
     * @param ClientTransformer $transformer
     */
    public function __construct(ClientTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the clients.
     *
     * @param ClientFilter $filter
     * @return JsonResponse
     */
    public function index(ClientFilter $filter)
    {
        $clients = new Paginate(Client::filter($filter));

        return $this->respondWithPagination($clients);
    }

//    public function store(CreateArticle $request)
//    {
//        $user = auth()->user();
//
//        $article = $user->articles()->create([
//            'title' => $request->input('article.title'),
//            'description' => $request->input('article.description'),
//            'body' => $request->input('article.body'),
//        ]);
//
//        $inputTags = $request->input('article.tagList');
//
//        if ($inputTags && ! empty($inputTags)) {
//
//            $tags = array_map(function($name) {
//                return Tag::firstOrCreate(['name' => $name])->id;
//            }, $inputTags);
//
//            $article->tags()->attach($tags);
//        }
//
//        return $this->respondWithTransformer($article);
//    }
//
//    /**
//     * Get the article given by its slug.
//     *
//     * @param Article $article
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function show(Article $article)
//    {
//        return $this->respondWithTransformer($article);
//    }
//
//    /**
//     * Update the article given by its slug and return the article if successful.
//     *
//     * @param UpdateArticle $request
//     * @param Article $article
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function update(UpdateArticle $request, Article $article)
//    {
//        if ($request->has('article')) {
//            $article->update($request->get('article'));
//        }
//
//        return $this->respondWithTransformer($article);
//    }
//
//    /**
//     * Delete the article given by its slug.
//     *
//     * @param DeleteArticle $request
//     * @param Article $article
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroy(DeleteArticle $request, Article $article)
//    {
//        $article->delete();
//
//        return $this->respondSuccess();
//    }
}
