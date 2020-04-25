<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Instructor;
use App\SkiSchool\Filters\Admin\InstructorFilter;

use App\Skischool\Transformers\InstructorTransformer;

class InstructorController extends ApiController
{
    /**
     * InstructorController constructor.
     *
     * @param InstructorTransformer $transformer
     */
    public function __construct(InstructorTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get all the instructors.
     *
     * @param InstructorFilter $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(InstructorFilter $filter)
    {
        $instructors = Instructor::filter($filter)->get();

        return $this->respondWithTransformer($instructors);
    }

    /**
     * Create a new article and return the article if successful.
     *
     * @param CreateArticle $request
     * @return \Illuminate\Http\JsonResponse
     */
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
