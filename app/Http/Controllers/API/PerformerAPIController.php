<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\{CategoriesRequest, BecomePerformerEmailPhone, BecomePerformerRequest, GiveTaskRequest};
use App\Models\{User, UserCategory};
use App\Services\{Profile\ProfileService, PerformersService};
use Illuminate\Http\{JsonResponse, Request, Resources\Json\AnonymousResourceCollection};
use Illuminate\Support\Facades\Auth;

class PerformerAPIController extends Controller
{

    protected ProfileService $profileService;
    private PerformersService $performer_service;

    public function __construct()
    {
        $this->performer_service = new PerformersService();
        $this->profileService = new ProfileService();
    }

    /**
     * @OA\Get(
     *     path="/api/performers",
     *     tags={"Performers"},
     *     summary="Get All Performers",
     *     @OA\Parameter (
     *          in="query",
     *          name="online",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     * )
     *
     */
    public function performers(Request $request): JsonResponse
    {
        $online = $request->online;
        $per_page = $request->get('per_page');
        return $this->performer_service->performers($online, $per_page);
    }

    /**
     * @OA\Get(
     *     path="/api/performers-filter",
     *     tags={"Performers"},
     *     summary="Performers Filter",
     *     @OA\Parameter (
     *          in="query",
     *          name="search",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="categories",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="child_categories",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="online",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="alphabet",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="review",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="desc",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="asc",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     *
     */
    public function performer_filter(Request $request): AnonymousResourceCollection
    {
        $data = $request->all();
        $authId = Auth::id();
        return $this->performer_service->performer_filter($data, $authId);
    }

    /**
     * @OA\Post(
     *     path="/api/give-task",
     *     tags={"Task"},
     *     summary="Give task by task ID and perfomer ID",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="performer_id",
     *                    type="integer",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function give_task(GiveTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $task_id = $data['task_id'];
        $performer_id = $data['performer_id'];

        return $this->performer_service->task_give_app($task_id, $performer_id);
    }

    /**
     * @OA\Post(
     *     path="/api/become-performer",
     *     tags={"Become a perfomer"},
     *     summary="Initial Data",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="name",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="location",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="born_date",
     *                    type="number",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function becomePerformerData(BecomePerformerRequest $request): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        return $this->performer_service->becomePerformerData($user, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/become-performer-phone",
     *     tags={"Become a perfomer"},
     *     summary="Email and phone",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="email",
     *                    type="string",
     *                    format="email",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function becomePerformerEmailPhone(BecomePerformerEmailPhone $request): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        return $this->performer_service->becomePerformerEmailPhone($user, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/become-performer-avatar",
     *     tags={"Become a perfomer"},
     *     summary="Avator",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="avatar",
     *                    type="file",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function becomePerformerAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $filename = $request->file('avatar');
        $this->profileService->changeAvatar($filename, $user);

        return response()->json(['success' => true, 'message' => 'success', 'data' => $filename]);


    }

    /**
     * @OA\Post(
     *     path="/api/become-performer-category",
     *     tags={"Become a perfomer"},
     *     summary="Categories",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="category_id",
     *                    type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function becomePerformerCategory(CategoriesRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = Auth::user();
        $categories = explode(",", $data['category_id']);
        $sms_notification = (int)$request->get('sms_notification');
        $email_notification = (int)$request->get('email_notification');
        $response = $this->profileService->subscribeToCategory($categories, $user, $sms_notification, $email_notification);
        return response()->json($response);

    }


    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     tags={"Performers"},
     *     summary="Performer reviews",
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function reviews(Request $request): JsonResponse
    {
        $from = $request->get('from');
        $type = $request->get('type');
        $authId = Auth::id();
        $reviews = $this->performer_service->reviews($from, $type, $authId);
        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Success'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/performers-count/{category_id}",
     *     tags={"Performers"},
     *     summary="Performer count",
     *     @OA\Parameter (
     *          in="path",
     *          name="category_id",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     * )
     */
    public function performers_count($category_id): JsonResponse
    {
        $user_category = UserCategory::query()->where('category_id', $category_id)->count();
        return response()->json([
            'success' => true,
            'data' => $user_category,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/performers-image/{category_id}",
     *     tags={"Performers"},
     *     summary="Performer image",
     *     @OA\Parameter (
     *          in="path",
     *          name="category_id",
     *          @OA\Schema (
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function performers_image($category_id): JsonResponse
    {
        $authId = \auth()->id();
        $images = $this->performer_service->performers_image($category_id, $authId);
        return response()->json([
            'success' => true,
            'data' => $images,
        ]);
    }

}
