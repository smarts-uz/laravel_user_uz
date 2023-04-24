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
     *     path="/api/performers-filter",
     *     tags={"Performers"},
     *     summary="Ijrochilarni filter qilish uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/196](https://t.me/c/1334612640/196).",
     *     @OA\Parameter (
     *          in="query",
     *          description="performerlarni name bo'yicha search qilish",
     *          name="search",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="ijrochilarni tanlagan parent kategoriyasi bo'yicha qaytaradi([2,3,7] - manashu formatda kiritiladi)",
     *          name="categories",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="ijrochilarni tanlagan child kategoriyasi bo'yicha qaytaradi([23,24,25] - manashu formatda kiritiladi)",
     *          name="child_categories",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="true bo'lsa onlayn ijrochilar qiymatini qaytaradi",
     *          name="online",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="true bo'lsa alphabet bo'yicha ijrochilar qiymatini qaytaradi",
     *          name="alphabet",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="true bo'lsa review bo'yicha ijrochilar qiymatini qaytaradi",
     *          name="review",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="true bo'lsa review,online,alphabet qiymatini kamayish bo'yicha ijrochilar qiymatini qaytaradi",
     *          name="desc",
     *          @OA\Schema (
     *              type="boolean"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="true bo'lsa review,online,alphabet qiymatini o'sish bo'yicha ijrochilar qiymatini qaytaradi",
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
     *     description="[**Telegram :** https://t.me/c/1334612640/137](https://t.me/c/1334612640/137).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="task_id",
     *                    description="foydalanuvchi ijrochiga bajartirmoqchi bo'lgan vazifasi idsini kiritadi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="performer_id",
     *                    description="bajartirmoqchi bo'lgan ijrochining idsi kiritiladi",
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
     *     summary="Ijrochi bo'lishda shaxsiy ma'lumotlarni kiritish uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/140](https://t.me/c/1334612640/140).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="name",
     *                    description="Ijrochi bo'lmoqchi bo'lgan foydalanuvchi o'zining nomini kiritadi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="location",
     *                    description="Ijrochi bo'lmoqchi bo'lgan foydalanuvchi o'zining manzilini kiritadi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="born_date",
     *                    description="Ijrochi bo'lmoqchi bo'lgan foydalanuvchi o'zining tug'ilgan kunini kiritad.('2000-03-19'-shu formatda)",
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
     *     summary="Ijrochi bo'lishda telefon raqam va email kiritish uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/186](https://t.me/c/1334612640/186).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="email",
     *                    description="Ijrochi bo'lmoqchi bo'lgan foydalanuvchi o'zining emailini kiritadi",
     *                    type="string",
     *                    format="email",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
     *                    description="Ijrochi bo'lmoqchi bo'lgan foydalanuvchi o'zining telefon raqamini kiritadi",
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
     *     summary="Ijrochi bo'lishda profiliga rasm qo'yish uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/187](https://t.me/c/1334612640/187).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="avatar",
     *                    description="Ijrochi bo'lmoqchi bo'lgan foydalanuvchi o'zining rasmini kiritadi",
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
     *     summary="Ijrochi bo'lishda kategoriya kiritish uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/188](https://t.me/c/1334612640/188).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="category_id",
     *                    description="Child category id kiritiladi.(masalan - '31,32,33')",
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
     *     path="/api/performers-count/{categoryId}",
     *     tags={"Performers"},
     *     summary="Ijrochilarning ma'lum kategoriya bo'yicha soni",
     *     description="[**Telegram :** https://t.me/c/1334612640/162](https://t.me/c/1334612640/162).",
     *     @OA\Parameter (
     *          in="path",
     *          description="child kategoriya id kiritiladi",
     *          name="categoryId",
     *          required=true,
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
    public function performers_count($categoryId): JsonResponse
    {
        $user_category = UserCategory::query()->where('category_id', $categoryId)->count();
        return response()->json([
            'success' => true,
            'data' => $user_category,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/performers-image/{categoryId}",
     *     tags={"Performers"},
     *     summary="Ijrochilarning ma'lum kategoriya bo'yicha rasmlari uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/231](https://t.me/c/1334612640/231).",
     *     @OA\Parameter (
     *          in="path",
     *          description="child kategoriya id kiritiladi",
     *          required=true,
     *          name="categoryId",
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
    public function performers_image($categoryId): JsonResponse
    {
        $authId = auth()->id();
        $images = $this->performer_service->performers_image($categoryId, $authId);
        return response()->json([
            'success' => true,
            'data' => $images,
        ]);
    }

}
