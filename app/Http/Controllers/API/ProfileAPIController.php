<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\{Api\CategoryRequest,
    Api\PortfolioRequest,
    Api\ProfileAvatarRequest,
    Api\ProfilePasswordRequest,
    Api\ProfilePhoneRequest,
    Api\ProfileSettingsRequest,
    Api\ProfileVideoRequest,
    Api\ResponseTemplateRequest,
    Api\UserReportRequest,
    UserBlockRequest};
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
        $user = auth()->user()->id;
        return $this->profileService->portfolios($user);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/create",
     *     tags={"Profile"},
     *     summary="Portfolio Create",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="images",
     *                    type="file",
     *                 ),
     *                 @OA\Property (
     *                    property="comment",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
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
     *     path="/api/profile/portfolio/{portfolio}/delete",
     *     tags={"Profile"},
     *     summary="Delete Portfolio",
     *     @OA\Parameter(
     *          in="path",
     *          name="portfolio",
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
    public function portfolioDelete(Portfolio $portfolio): JsonResponseAlias
    {
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
     *     path="/api/portfolio/{portfolio}/update",
     *     tags={"Profile"},
     *     summary="Portfolio Update",
     *     @OA\Parameter(
     *          in="path",
     *          name="portfolio",
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
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="image",
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
     * @throws JsonException
     */
    public function portfolioUpdate(PortfolioRequest $request, Portfolio $portfolio): JsonResponseAlias
    {
        $hasFile = $request->hasFile('images');
        $files = $request->file('images');
        $description = $request->get('description');
        $comment = $request->get('comment');
        $portfolios = $this->profileService->updatePortfolio($hasFile, $files, $portfolio, $description, $comment);
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="link",
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
        $user = auth()->user();
        $performer = $request->get('performer');
        $review = $request->get('review');
        $reviews = ProfileService::userReviews($user, $performer, $review);
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
     *     @OA\Parameter (
     *          in="query",
     *          name="type",
     *          description="in yoki out kiritiladi",
     *          @OA\Schema (
     *              type="string",
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="period",
     *          description="month, week yoki year kiritiladi",
     *          @OA\Schema (
     *              type="string",
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="from",
     *          description="boshlang'ich vaqti kiritiladi(2022-11-01  shu formatda)",
     *          @OA\Schema (
     *              type="string",
     *              format="date-time"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          name="to",
     *          description="oxirgi vaqti kiritiladi(2022-12-01  shu formatda)",
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

        $data = $this->profileService->balance($period, $from, $to, $type);
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
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
     * @throws \Exception
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="old_password",
     *                    type="string",
     *                    format="password",
     *                 ),
     *                 @OA\Property (
     *                    property="password",
     *                    type="string",
     *                    format="password",
     *                 ),
     *                 @OA\Property (
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
     *                    property="gender",
     *                    description="1 yoki 0",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="born_date",
     *                    description="2022-06-03 - manashu formatda kiritiladi",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="age",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="email",
     *                    type="string",
     *                    format="email",
     *                 ),
     *                 @OA\Property (
     *                    property="location",
     *                    description="Bo`sh qoldirsa boladi",
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
        $data = [
            'name' => $user->name,
            'last_name' => $user->last_name,
            'avatar' => $user->avatar,
            'location' => $user->location,
            'date_of_birth' => $user->born_date,
            'email' => $user->email,
            'phone' => (new CustomService)->correctPhoneNumber($user->phone_number),
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="notification",
     *                    type="integer",
     *                    description="0 yoki 1 kiritiladi, 0 bo'lsa o'chiriladi, 1 bo'lsa yoqiladi",
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
     *     path="/api/profile/{id}",
     *     tags={"Profile"},
     *     summary="Get Profile By ID",
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
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
    public function userProfile(User $user): JsonResponseAlias
    {
        $user_id = ($user !== null) ? $user->id : 0;
        (new PerformersService)->setView($user);
        return $this->profileService->index($user_id);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/{user}/portfolios",
     *     tags={"Profile"},
     *     summary="User Portfolios",
     *     @OA\Parameter(
     *          in="path",
     *          name="user",
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
    public function userPortfolios($id): JsonResponseAlias
    {
        return $this->profileService->portfolios($id);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/{user}/reviews",
     *     tags={"Profile"},
     *     summary="User Reviews",
     *     @OA\Parameter(
     *          in="path",
     *          name="user",
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
    public function userReviews(Request $request, User $user): JsonResponseAlias
    {
        $performer = $request->get('performer');
        $review = $request->get('review');
        $reviews = ProfileService::userReviews($user, $performer, $review);
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  @OA\Property (
     *                    property="sms_notification",
     *                    type="integer",
     *                 ),
     *                  @OA\Property (
     *                    property="email_notification",
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
     *     summary="Change lang",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="lang",
     *                    description="ru yoki uz",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="version",
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
     * @throws \Exception
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
        return $this->profileService->blocked_user_list($user_id);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/{portfolio}/delete-image",
     *     tags={"Profile"},
     *     summary="Profile delete image",
     *     @OA\Parameter(
     *          in="path",
     *          name="portfolio",
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
     *                    description="Ozodbek/1675856445_image_2023-02-07_11-00-21.png shunday ko'rinishda img url kiritiladi",
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
    public function deleteImage(Request $request, Portfolio $portfolio): JsonResponseAlias
    {
        $image = $request->get('image');
        $this->profileService->deleteImage($image,$portfolio);
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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="work_experience",
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
        $user = auth()->user();
        return $this->profileService->response_template($user);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/response-template/edit/{id}",
     *     tags={"Profile"},
     *     summary="Profile response template edit",
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
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
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="text",
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
    public function response_template_edit(ResponseTemplateRequest $request, ResponseTemplate $id): JsonResponseAlias
    {
        $data = $request->validated();
        $id->update($data);

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
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="title",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="text",
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
     *     path="/api/profile/response-template/delete/{id}",
     *     tags={"Profile"},
     *     summary="Profile response template delete",
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
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
    public function response_template_delete(ResponseTemplate $template): JsonResponseAlias
    {
        /** @var User $user */
        $user = auth()->user();
        return $this->profileService->response_template_delete($user, $template);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/notification-off",
     *     tags={"Profile"},
     *     summary="Notification on or off",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="notification_off",
     *                    type="integer",
     *                    description="0 yoki 1, 0 bo'lsa yoqish, 1 bo'lsa o'chirish"
     *                 ),
     *                 @OA\Property (
     *                    property="notification_to",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
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


}
