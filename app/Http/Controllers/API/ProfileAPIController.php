<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PortfolioRequest;
use App\Http\Requests\Api\ProfileAvatarRequest;
use App\Http\Requests\Api\ProfilePasswordRequest;
use App\Http\Requests\Api\ProfilePhoneRequest;
use App\Http\Requests\Api\ProfileSettingsRequest;
use App\Http\Requests\Api\ProfileVideoRequest;
use App\Http\Requests\Api\UserReportRequest;
use App\Http\Resources\PortfolioIndexResource;
use App\Http\Resources\ReviewIndexResource;
use App\Http\Resources\UserIndexResource;
use App\Models\Portfolio;
use App\Models\ReportedUser;
use App\Models\User;
use App\Services\Profile\ProfileService;
use App\Services\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function React\Promise\all;

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
    public function index(): UserIndexResource
    {
        $user = Auth::user();
        return new UserIndexResource($user);
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
    public function portfolios(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'data' => PortfolioIndexResource::collection(Portfolio::query()->where(['user_id' => $user->id])->get())
        ]);
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
    public function portfolioCreate(PortfolioRequest $request): JsonResponse
    {
        $portfolio = $this->profileService->createPortfolio($request);
        return response()->json([
            'success' => true,
            'data' => new PortfolioIndexResource($portfolio)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/{portfolio}/delete",
     *     tags={"Search"},
     *     summary="Delete Portfolio",
     *     @OA\Parameter(
     *          in="path",
     *          name="portfolio",
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
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function portfolioDelete(Portfolio $portfolio): JsonResponse
    {
        portfolioGuard($portfolio);
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
     *              type="string"
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
    public function portfolioUpdate(PortfolioRequest $request, Portfolio $portfolio): JsonResponse
    {
        $portfolio = $this->profileService->updatePortfolio($request, $portfolio);
        return response()->json([
            'success' => true,
            'data' => new PortfolioIndexResource($portfolio)
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
    public function videoStore(ProfileVideoRequest $request): JsonResponse
    {
        $response = $this->profileService->videoStore($request);
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
    public function videoDelete(): JsonResponse
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
    public function reviews(Request $request): JsonResponse
    {
        $user = auth()->user();
        $reviews = ProfileService::userReviews($user, $request);
        return response()->json([
            'success' => true,
            'data' => ReviewIndexResource::collection($reviews)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/balance",
     *     tags={"Profile"},
     *     summary="Profile balance",
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
    public function balance(Request $request): JsonResponse
    {
        $data = $this->profileService->balance($request);
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
    public function phoneEdit(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        return response()->json([
            'data' => [
                'phone_number' => correctPhoneNumber($user->phone_number)
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
     */
    public function phoneUpdate(ProfilePhoneRequest $request): JsonResponse
    {
        $response = $this->profileService->phoneUpdate($request);
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
    public function change_password(ProfilePasswordRequest $request): JsonResponse
    {
        return $this->profileService->changePassword($request->validated());
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
    public function avatar(ProfileAvatarRequest $request): JsonResponse
    {
        $this->profileService->changeAvatar($request);
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
     *                    description="2022-06-03 12:00:0 - manashu formatda kiritiladi",
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
    public function updateData(ProfileSettingsRequest $request): JsonResponse
    {
        $this->profileService->updateSettings($request);
        $message = trans('trans.Settings updated successfully.');
        return response()->json([
            'success' => true,
            'message' => $message
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
    public function editData(): JsonResponse
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
            'phone' => correctPhoneNumber($user->phone_number),
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
    public function editDesctiption(Request $request): JsonResponse
    {
        $profile = new ProfileService();
        $profile->editDescription($request);

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
     *     summary="Profile edit description",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="notification",
     *                    type="boolean",
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
    public function userNotifications(Request $request): JsonResponse
    {
        $message = $this->profileService->notifications($request);
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
    public function userProfile(User $user): JsonResponse
    {
        setview($user);
        return response()->json([
            'success' => true,
            'data' => new UserIndexResource($user)
        ]);
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
    public function userPortfolios($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => PortfolioIndexResource::collection(Portfolio::query()->where(['user_id' => $id])->get())
        ]);
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
    public function userReviews(Request $request, User $user): JsonResponse
    {
        $reviews = ProfileService::userReviews($user, $request);
        return response()->json([
            'success' => true,
            'data' => ReviewIndexResource::collection($reviews)
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/categories-subscribe",
     *     tags={"Profile"},
     *     summary="Category subscribe",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="category",
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
    public function subscribeToCategory(Request $request): JsonResponse
    {
        $response = $this->profileService->subscribeToCategory($request);
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
    public function changeLanguage(Request $request): JsonResponse
    {
        if (Auth::guard('api')->check()) {
            cache()->forever('lang' . auth()->id(), $request->get('lang'));
            app()->setLocale($request->get('lang'));
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => trans('trans.Language changed successfully.')
                ]
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => trans('trans.Language changed successfully.')
        ]);
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
     */
    public function selfDelete(): JsonResponse
    {
        /** @var User $user */
        $user = \auth()->user();
        if ($user->phone_number && strlen($user->phone_number) === 13) {
            VerificationService::send_verification('phone', $user, $user->phone_number);
            return response()->json([
                'success' => true,
                'phone_number' => correctPhoneNumber($user->phone_number),
                'message' => __('СМС-код отправлен!')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('Ваш номер не подтвержден')
            ]);
        }
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
    public function confirmationSelfDelete(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = \auth()->user();
        if ($user->verify_code == $request->get('code')) {
            if (strtotime($user->verify_expiration) >= strtotime(now())) {
                $user->delete();
                return response()->json([
                    'success' => true,
                    'message' => __('Успешно удалено')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Срок действия номера истек')
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('Код ошибки')
            ]);
        }
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
     *                    property="user_id",
     *                    description="Current user",
     *                    type="string",
     *                 ),
     *                @OA\Property (
     *                    property="reported_user_id",
     *                    description="Reported user id",
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
    public function report(UserReportRequest $request)
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
            'message' => __('Сохранено')
        ]);
    }
}
