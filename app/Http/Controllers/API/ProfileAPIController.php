<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\{Api\CategoryRequest, Api\PortfolioRequest, Api\ProfileAvatarRequest,
    Api\ProfilePasswordRequest, Api\ProfilePhoneRequest, Api\ProfileSettingsRequest, Api\ProfileVideoRequest,
    Api\ResponseTemplateRequest, Api\UserReportRequest, UserBlockRequest};
use Exception;
use JsonException;
use App\Models\{Portfolio, ReportedUser, ResponseTemplate, User};
use App\Services\{CustomService, PerformersService, Profile\ProfileService};
use Illuminate\Http\JsonResponse as JsonResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileAPIController extends Controller
{
    protected ProfileService $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    /**
     * @OA\Get(
     *     path="/api/profile/",
     *     tags={"Profile"},
     *     summary="Profile index",
     *     description="[**Telegram :** https://t.me/c/1334612640/127](https://t.me/c/1334612640/127).",
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
    public function index()
    {
        $user = Auth::user();
        $user_id = ($user !== null) ? $user->id : 0;
        return $this->profileService->index($user_id);
    }


    /**
     * @OA\Get(
     *     path="/api/profile/portfolios",
     *     tags={"Profile"},
     *     summary="Profile portfolios",
     *     description="[**Telegram :** https://t.me/c/1334612640/128](https://t.me/c/1334612640/128).",
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
    public function portfolios(): JsonResponseAlias
    {
        /** @var User $user */
        $userId = auth()->user()->id;
        $data = $this->profileService->portfolios($userId);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/create",
     *     tags={"Profile"},
     *     summary="Portfolio Create",
     *     description="[**Telegram :** https://t.me/c/1334612640/129](https://t.me/c/1334612640/129).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="comment",
     *                    description="portfolio uchun comment yoziladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    description="portfolio uchun tavsif yoziladi",
     *                    type="string",
     *                 ),
     *                @OA\Property (
     *                    property="images[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="file",
     *                      @OA\Property(
     *                          property="image",
     *                          description="portfolio uchun rasm kiritiladi",
     *                      ),
     *                    ),
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
    public function portfolioCreate(PortfolioRequest $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->except('images');
        $hasFile = $request->hasFile('images');
        $files = $request->file('images');

        $portfolio = $this->profileService->createPortfolio($user, $data, $hasFile, $files);
        return response()->json([
            'success' => true,
            'data' => $portfolio
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/profile/portfolio/{portfolioId}/delete",
     *     tags={"Profile"},
     *     summary="Delete Portfolio",
     *     description="[**Telegram :** https://t.me/c/1334612640/172](https://t.me/c/1334612640/172).",
     *     @OA\Parameter(
     *          in="path",
     *          description="portfolio id kiritiladi",
     *          name="portfolioId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\Response(
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
    public function portfolioDelete($portfolioId): JsonResponseAlias
    {
        $portfolio = Portfolio::find($portfolioId);
        (new ProfileService)->portfolioGuard($portfolio);
        $portfolio->delete();
        $message = trans('trans.Portfolio deleted successfully.');

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/portfolio/{portfolioId}/update",
     *     tags={"Profile"},
     *     summary="Portfolio Update",
     *     description="[**Telegram :** https://t.me/c/1334612640/227](https://t.me/c/1334612640/227).",
     *     @OA\Parameter(
     *          in="path",
     *          description="portfolio id kiritiladi",
     *          name="portfolioId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="comment",
     *                    description="portfolio uchun comment yoziladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    description="portfolio uchun tavsif yoziladi",
     *                    type="string",
     *                 ),
     *                @OA\Property (
     *                    property="image[]",
     *                    type="array",
     *                    @OA\Items(
     *                      type="file",
     *                      @OA\Property(
     *                          property="image",
     *                          description="portfolio uchun rasm kiritiladi",
     *                      ),
     *                    ),
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
     * @throws JsonException
     */
    public function portfolioUpdate(PortfolioRequest $request, $portfolioId): JsonResponseAlias
    {
        $hasFile = $request->hasFile('images');
        $files = $request->file('images');
        $description = $request->get('description');
        $comment = $request->get('comment');
        $portfolios = $this->profileService->updatePortfolio($hasFile, $files, $portfolioId, $description, $comment);
        return response()->json([
            'success' => true,
            'data' => $portfolios
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/video",
     *     tags={"Profile"},
     *     summary="Profile Video Store",
     *     description="[**Telegram :** https://t.me/c/1334612640/130](https://t.me/c/1334612640/130).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="link",
     *                    description="youtobe video link yoziladi(masalan https://www.youtube.com/watch?v=iU-Uk5CEG1k&ab_channel=KunUZ)",
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
    public function videoStore(ProfileVideoRequest $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $validated = $request->validated();
        $link = $validated['link'];
        $response = $this->profileService->videoStore($user, $link);
        return response()->json($response);
    }


    /**
     * @OA\Delete(
     *     path="/api/video/delete",
     *     tags={"Profile"},
     *     summary="Profile video delete",
     *     description="[**Telegram :** https://t.me/c/1334612640/195](https://t.me/c/1334612640/195).",
     *     @OA\Response(
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
    public function videoDelete(): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $user->youtube_link = null;
        $user->save();

        $message = trans('trans.Video deleted successfully.');
        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/reviews",
     *     tags={"Profile"},
     *     summary="Profile reviews",
     *     description="[**Telegram :** https://t.me/c/1334612640/131](https://t.me/c/1334612640/131).",
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
    public function reviews(Request $request): JsonResponseAlias
    {
        $userId = auth()->id();
        $performer = $request->get('performer');
        $review = $request->get('review');
        $reviews = ProfileService::userReviews($userId, $performer, $review);
        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/balance",
     *     tags={"Profile"},
     *     summary="Profile balance",
     *     description="[**Telegram :** https://t.me/c/1334612640/132](https://t.me/c/1334612640/132).",
     *     @OA\Parameter (
     *          in="query",
     *          name="type",
     *          description="in yoki out kiritiladi",
     *          @OA\Schema (
     *              type="string",
     *              enum={"in","out"},
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="period",
     *          description="month, week yoki year kiritiladi",
     *          @OA\Schema (
     *              enum={"month","week","year"},
     *              type="string",
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="from",
     *          description="boshlang'ich vaqti kiritiladi(2022-11-01 shu formatda)",
     *          @OA\Schema (
     *              type="string",
     *              format="date-time"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="to",
     *          description="oxirgi vaqti kiritiladi(2022-12-01 shu formatda)",
     *          @OA\Schema (
     *              type="string",
     *              format="date-time"
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
    public function balance(Request $request): JsonResponseAlias
    {
        $period = $request->get('period');
        $from = $request->get('from');
        $to = $request->get('to');
        $type = $request->get('type');
        $userId = \auth()->id();
        $data = $this->profileService->balance($period, $from, $to, $type, $userId);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/profile/settings/phone",
     *     tags={"Profile Settings"},
     *     summary="Phone",
     *     description="[**Telegram :** https://t.me/c/1334612640/148](https://t.me/c/1334612640/148).",
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
    public function phoneEdit(): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        return response()->json([
            'data' => [
                'phone_number' => (new CustomService)->correctPhoneNumber($user->phone_number)
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/settings/phone/edit",
     *     tags={"Profile Settings"},
     *     summary="Phone edit",
     *     description="[**Telegram :** https://t.me/c/1334612640/149](https://t.me/c/1334612640/149).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="phone_number",
     *                    description="phone_number yoziladi",
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
     * @throws Exception
     */
    public function phoneUpdate(ProfilePhoneRequest $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $phoneNumber = $request->get('phone_number');
        $response = $this->profileService->phoneUpdate($phoneNumber, $user);
        return response()->json($response);

    }


    /**
     * @OA\Post(
     *     path="/api/profile/settings/password/change",
     *     tags={"Profile Settings"},
     *     summary="Change password",
     *     description="[**Telegram :** https://t.me/c/1334612640/150](https://t.me/c/1334612640/150).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="avvalgi parol yoziladi",
     *                    property="old_password",
     *                    type="string",
     *                    format="password",
     *                 ),
     *                 @OA\Property (
     *                    description="yangi parol yoziladi",
     *                    property="password",
     *                    type="string",
     *                    format="password",
     *                 ),
     *                 @OA\Property (
     *                    description="yangi parol yoziladi",
     *                    property="password_confirmation",
     *                    type="string",
     *                    format="password",
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
    public function change_password(ProfilePasswordRequest $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->validated();
        return $this->profileService->changePassword($user, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/settings/change-avatar",
     *     tags={"Profile Settings"},
     *     summary="Change Avator",
     *     description="[**Telegram :** https://t.me/c/1334612640/151](https://t.me/c/1334612640/151).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="Profile uchun rasm kiritiladi",
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
    public function avatar(ProfileAvatarRequest $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $filename = $request->file('avatar');
        $this->profileService->changeAvatar($filename, $user);
        $message = trans('file.Photo updated successfully.');
        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/settings/update",
     *     tags={"Profile Settings"},
     *     summary="Update settings",
     *     description="[**Telegram :** https://t.me/c/1334612640/152](https://t.me/c/1334612640/152).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="name",
     *                    description="foydalanuvchi nomi kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="gender",
     *                    description="1 yoki 0",
     *                    type="string",
     *                    enum={"0","1"},
     *                 ),
     *                 @OA\Property (
     *                    property="born_date",
     *                    description="2022-06-03 - manashu formatda kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="age",
     *                    description="yoshi kiritiladi",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    description="email kiritiladi",
     *                    property="email",
     *                    type="string",
     *                    format="email",
     *                 ),
     *                 @OA\Property (
     *                    property="location",
     *                    description="yashash manzili kiritiladi (bo`sh qoldirsa boladi)",
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
    public function updateData(ProfileSettingsRequest $request): JsonResponseAlias
    {
        $validated = $request->validated();
        unset($validated['age']);
        /** @var User $user */
        $user = auth()->user();
        $this->profileService->updateSettings($validated, $user);
        $message = trans('trans.Settings updated successfully.');
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $validated
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/profile/settings",
     *     tags={"Profile Settings"},
     *     summary="Your profile data",
     *     description="[**Telegram :** https://t.me/c/1334612640/153](https://t.me/c/1334612640/153).",
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
    public function editData(): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        if($user->is_phone_number_verified){
            $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
        }else{
            $phone_number = '';
        }
        $data = [
            'name' => $user->name,
            'last_name' => $user->last_name,
            'avatar' => $user->avatar,
            'location' => $user->location,
            'date_of_birth' => $user->born_date,
            'email' => $user->email,
            'phone' => $phone_number,
            'gender' => $user->gender,
        ];
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/description/edit",
     *     tags={"Profile"},
     *     summary="Profile edit description",
     *     description="[**Telegram :** https://t.me/c/1334612640/133](https://t.me/c/1334612640/133).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    description="profili uchun tavsif yoziladi",
     *                    property="description",
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
    public function editDescription(Request $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = Auth::user();
        $user->description = $request->get('description');
        $user->save();

        $message = trans('trans.Description updated successfully.');
        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message,
                'description' => $request->get('description')
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/settings/notifications",
     *     tags={"Profile Settings"},
     *     summary="Profile edit news notifications",
     *     description="[**Telegram :** https://t.me/c/1334612640/154](https://t.me/c/1334612640/154).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="notification",
     *                    type="string",
     *                    description="0 yoki 1 tanlanadi, 0 bo'lsa o'chiriladi, 1 bo'lsa yoqiladi",
     *                    enum={"0", "1"},
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
    public function userNotifications(Request $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $notification = $request->get('notification');
        $message = $this->profileService->notifications($user, $notification);
        return response()->json([
            'success' => true,
            'data' => [
                'message' => $message
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/{userId}",
     *     tags={"Profile"},
     *     summary="Get Profile By ID",
     *     description="[**Telegram :** https://t.me/c/1334612640/134](https://t.me/c/1334612640/134).",
     *     @OA\Parameter(
     *          in="path",
     *          description="foydalanuvchi idsi kiritiladi",
     *          name="userId",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
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
     */
    public function userProfile($userId): JsonResponseAlias
    {
        $user = User::find($userId);
        (new PerformersService)->setView($user);
        return $this->profileService->index($userId);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/{userId}/portfolios",
     *     tags={"Profile"},
     *     summary="User Portfolios",
     *     description="[**Telegram :** https://t.me/c/1334612640/135](https://t.me/c/1334612640/135).",
     *     @OA\Parameter(
     *          in="path",
     *          description="foydalanuvchi idsi kiritiladi",
     *          name="userId",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
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
     */
    public function userPortfolios($userId): JsonResponseAlias
    {
        $data = $this->profileService->portfolios($userId);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/{userId}/reviews",
     *     tags={"Profile"},
     *     summary="User Reviews",
     *     description="[**Telegram :** https://t.me/c/1334612640/136](https://t.me/c/1334612640/136).",
     *     @OA\Parameter(
     *          in="path",
     *          description="foydalanuvchi idsi kiritiladi",
     *          name="userId",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
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
     */
    public function userReviews(Request $request, $userId): JsonResponseAlias
    {
        $performer = $request->get('performer');
        $review = $request->get('review');
        $reviews = ProfileService::userReviews($userId, $performer, $review);
        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/categories-subscribe",
     *     tags={"Profile"},
     *     summary="Category subscribe",
     *     description="[**Telegram :** https://t.me/c/1334612640/162](https://t.me/c/1334612640/162).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  @OA\Property (
     *                    property="category",
     *                    description="child kategoriya idlar kiritiladi",
     *                    type="string",
     *                  ),
     *                  @OA\Property (
     *                    property="sms_notification",
     *                    description="sms_notificationni yoqish uchun 1, o'chirish uchun 0",
     *                    enum={"0", "1"},
     *                    type="string",
     *                 ),
     *                  @OA\Property (
     *                    property="email_notification",
     *                    description="email_notificationni yoqish uchun 1, o'chirish uchun 0",
     *                    enum={"0", "1"},
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
    public function subscribeToCategory(CategoryRequest $request): JsonResponseAlias
    {

        $data = $request->validated();

        /** @var User $user */
        $user = auth()->user();
        $categories = $data['category'];

        $sms_notification = (int)$request->get('sms_notification');
        $email_notification = (int)$request->get('email_notification');

        $response = $this->profileService->subscribeToCategory($categories, $user, $sms_notification, $email_notification);
        return response()->json($response);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/settings/change-lang",
     *     tags={"Profile Settings"},
     *     summary="Change language",
     *     description="[**Telegram :** https://t.me/c/1334612640/155](https://t.me/c/1334612640/155).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="lang",
     *                    description="ru yoki uz",
     *                    type="string",
     *                    enum={"ru", "uz"}
     *                 ),
     *                 @OA\Property (
     *                    property="version",
     *                    description="app versiya kiritiladi (masalan 1.0.45)",
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
    public function changeLanguage(Request $request): JsonResponseAlias
    {
        $lang = $request->get('lang');
        $version = $request->get('version');
        return $this->profileService->changeLanguage($lang, $version);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/self-delete",
     *     tags={"Profile"},
     *     summary="Self delete",
     *     description="[**Telegram :** https://t.me/c/1334612640/215](https://t.me/c/1334612640/215).",
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
     * @throws Exception
     */
    public function selfDelete(): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        return $this->profileService->self_delete($user);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/confirmation-self-delete",
     *     tags={"Profile"},
     *     summary="Confirmation self delete",
     *     description="[**Telegram :** https://t.me/c/1334612640/256](https://t.me/c/1334612640/256).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="code",
     *                    description="confirmation code",
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
    public function confirmationSelfDelete(Request $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = \auth()->user();
        $code = $request->get('code');
        return $this->profileService->confirmationSelfDelete($user, $code);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/report-user",
     *     tags={"Profile"},
     *     summary="Report user",
     *     description="[**Telegram :** https://t.me/c/1334612640/216](https://t.me/c/1334612640/216).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="reported_user_id",
     *                    description="Reported user id",
     *                    type="string",
     *                 ),
     *                @OA\Property (
     *                    property="message",
     *                    description="Report message",
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
    public function report(UserReportRequest $request): JsonResponseAlias
    {
        $data = $request->validated();
        ReportedUser::query()->updateOrCreate([
            'user_id' => \auth()->id(),
            'reported_user_id' => $data['reported_user_id'],
        ], [
            'message' => $data['message']
        ]);
        return response()->json([
            'success' => true,
            'message' => __('Сохранено'),
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/block-user",
     *     tags={"Profile"},
     *     summary="Block user",
     *     description="[**Telegram :** https://t.me/c/1334612640/217](https://t.me/c/1334612640/217).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="blocked_user_id",
     *                    description="Blocked user id",
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

    public function block(UserBlockRequest $request): JsonResponseAlias
    {
        $data = $request->validated();
        $blocked_user_id = $data['blocked_user_id'];
        $user_id = auth()->id();
        return $this->profileService->blocked_user($blocked_user_id, $user_id);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/block-user-list",
     *     tags={"Profile"},
     *     summary="Block user list",
     *     description="[**Telegram :** https://t.me/c/1334612640/252](https://t.me/c/1334612640/252).",
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
    public function block_user_list(): JsonResponseAlias
    {
        $user_id = auth()->id();
        $data = $this->profileService->blocked_user_list($user_id);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/{portfolioId}/delete-image",
     *     tags={"Profile"},
     *     summary="Profile delete image",
     *     description="[**Telegram :** https://t.me/c/1334612640/218](https://t.me/c/1334612640/218).",
     *     @OA\Parameter(
     *          in="path",
     *          description="portfolio id kiritiladi",
     *          name="portfolioId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="image",
     *                    description="Username/1675856445_image_2023-02-07_11-00-21.png shunday ko'rinishda rasm url kiritiladi",
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
     * @throws JsonException
     */
    public function deleteImage(Request $request, $portfolioId): JsonResponseAlias
    {
        $image = $request->get('image');
        $this->profileService->deleteImage($image,$portfolioId);
        return response()->json([
            'success' => true,
            'message' => __('Успешно удалено'),
            'data' => $image
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/work-experience",
     *     tags={"Profile"},
     *     summary="Profile work experience",
     *     description="[**Telegram :** https://t.me/c/1334612640/202](https://t.me/c/1334612640/202).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="work_experience",
     *                    description="ish tajribasi kiritiladi(necha yilligi)",
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
    public function work_experience(Request $request): JsonResponseAlias
    {

        /** @var User $user */
        $user = Auth::user();
        $user->work_experience = $request->get('work_experience');
        $user->save();
        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('Успешно сохранено'),
                'work_experience' => $request->get('work_experience')
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/response-template",
     *     tags={"Profile"},
     *     summary="Profile response template",
     *     description="[**Telegram :** https://t.me/c/1334612640/244](https://t.me/c/1334612640/244).",
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
    public function response_template(): JsonResponseAlias
    {
        /** @var User $user */
        $userId = auth()->id();
        $data = $this->profileService->response_template($userId);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/response-template/edit/{templateId}",
     *     tags={"Profile"},
     *     summary="Profile response template edit",
     *     description="[**Telegram :** https://t.me/c/1334612640/162](https://t.me/c/1334612640/162).",
     *     @OA\Parameter(
     *          in="path",
     *          description="Javob qoldirish shabloni idsi kiritiladi",
     *          name="templateId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *     ),
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="title",
     *                    description="response template title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="text",
     *                    description="response template text",
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
    public function response_template_edit(ResponseTemplateRequest $request, ResponseTemplate $templateId): JsonResponseAlias
    {
        $data = $request->validated();
        $templateId->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено'),
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/response-template/create",
     *     tags={"Profile"},
     *     summary="Profile response template create",
     *     description="[**Telegram :** https://t.me/c/1334612640/246](https://t.me/c/1334612640/246).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="title",
     *                    description="response template title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="text",
     *                    description="response template text",
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
    public function response_template_create(ResponseTemplateRequest $request): JsonResponseAlias
    {

        $data = $request->validated();
        ResponseTemplate::query()->create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'text' => $data['text'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено'),
            'data' => $data
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/profile/response-template/delete/{templateId}",
     *     tags={"Profile"},
     *     summary="Profile response template delete",
     *     description="[**Telegram :** https://t.me/c/1334612640/248](https://t.me/c/1334612640/248).",
     *     @OA\Parameter(
     *          description="Javob qoldirish shabloni idsi kiritiladi",
     *          in="path",
     *          name="templateId",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          ),
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
    public function response_template_delete(ResponseTemplate $templateId): JsonResponseAlias
    {
        /** @var User $user */
        $userId = auth()->id();
        return $this->profileService->response_template_delete($userId, $templateId);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/notification-off",
     *     tags={"Profile"},
     *     summary="Notification on or off",
     *     description="[**Telegram :** https://t.me/c/1334612640/251](https://t.me/c/1334612640/251).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="notification_off",
     *                    type="integer",
     *                    description="0 yoki 1, 0 bo'lsa yoqish, 1 bo'lsa o'chirish",
     *                 ),
     *                 @OA\Property (
     *                    description="vaqt kiritiladi",
     *                    property="notification_to",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    description="vaqt kiritiladi",
     *                    property="notification_from",
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
    public function notification_off(Request $request): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        $user->notification_off = $request->get('notification_off');
        $user->notification_to = $request->get('notification_to');
        $user->notification_from = $request->get('notification_from');
        $user->save();
        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено'),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/categories",
     *     tags={"Profile"},
     *     summary="Profile categories",
     *     description="[**Telegram :** https://t.me/c/1334612640/260](https://t.me/c/1334612640/260).",
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
    public function userCategory(): array
    {
        $userId = \auth()->id();
        $data = $this->profileService->userCategory($userId);
        return ['data'=>$data];
    }


}
