<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdateDataRequest;
use App\Http\Resources\UserIndexResource;
use App\Models\Session;
use App\Models\Task;
use App\Models\User;
use App\Services\Profile\ProfileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile/pro",
     *     tags={"ProfileAPI"},
     *     summary="Your profile",
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
        return new UserIndexResource($user);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/password/change",
     *     tags={"ProfileAPI"},
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
    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => $validator->errors(),
            ]);
        }
        $user = auth()->user();
        if (Hash::check($request['old_password'], $user->password)) {
            $user->update(['password' => Hash::make($request['password'])]);

            return response()->json([
                'status' => true,
                'data' => [
                    'message' => "Password changed"
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data' => [
                    'message' => "Old password wrong"
                ]
            ]);
        }


    }

    /**
     * @OA\Post(
     *     path="/api/change-avatar",
     *     tags={"ProfileAPI"},
     *     summary="Change Avator",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
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
     */
    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image'
        ]);
        $user = Auth::user();
        $data = $request->all();
        $destination = 'storage/' . $user->avatar;
        if (File::exists($destination)) {
            File::delete($destination);
        }
        $filename = $request->file('avatar');
        $imagename = "user-avatar/" . $filename->getClientOriginalName();
        $filename->move(public_path() . '/storage/user-avatar/', $imagename);
        $data['avatar'] = $imagename;
        $user->update($data);

        return response()->json(['success' => true]);

    }

    public function settings()
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'location' => $user->location,
            'age' => $user->age,
            'role_id' => $user->role_id,
            'description' => $user->description,
        ]);
    }


    
    /**
     * @OA\Post(
     *     path="/api/profile/settings/update",
     *     tags={"PorfolioAPI"},
     *     summary="Update settings",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="email",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="age",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="phone_number",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="location",
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
    public function updateData(UserUpdateDataRequest $request)
    {
        $data = $request->validated();
        $profile = new ProfileService();
        $updatedData = $profile->settingsUpdate($data);
        $user = Auth::user();
        $user->update($updatedData);
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Settings updated'
            ]
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/profile/cash",
     *     tags={"ProfileAPI"},
     *     summary="Your cash",
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
    public function cash()
    {
        $user = Auth()->user()->load('transactions');

        $balance = $user->walletBalance;
        $views = $user->views()->count();
        $tasksCreated = $user->tasks()->count();
        $transactions = $user->transactions()->paginate(15);
        $about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $tasksPerformed = Task::where('performer_id', $user->id)->count();
        return response()->json([
            'user' => $user,
            'balance' => $balance,
            'views' => $views,
            'tasks_created' => $tasksCreated,
            'transactions' => $transactions,
            'about' => $about,
            'tasks_performed' => $tasksPerformed
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/profile/settings",
     *     tags={"ProfileAPI"},
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
    public function editData()
    {
        $profile = new ProfileService();
        $data = $profile->settingsEdit();
        return response()->json($data);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/sessions/clear",
     *     tags={"ProfileAPI"},
     *     summary="Session clear",
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
    public function clearSessions()
    {
        Session::query()->where('user_id', auth()->user()->id)->delete();
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Sessions cleared'
            ]
        ]);
    }


    /**
     * @OA\DELETE(
     *     path="/api/profile/delete",
     *     tags={"ProfileAPI"},
     *     summary="Delete User",
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
    public function deleteUser()
    {
        auth()->user()->delete();
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'User deleted'
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/category/update",
     *     tags={"ProfileAPI"},
     *     summary="Profile category update",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="category",
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
    public function updateCategory(Request $request)
    {
        $request->validate([
            'category' => 'required'
        ]);
        $user = Auth::user();
        $user->role_id = 2;
        $checkbox = implode(",", $request->get('category'));
        $user->update(['category_id' => $checkbox]);
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Category updated'
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/store/district",
     *     tags={"ProfileAPI"},
     *     summary="Profile district",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="district",
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
    public function storeDistrict(Request $request)
    {
        $request->validate([
            'district' => 'required',
        ]);

        $user = Auth::user();
        $user->district = $request->district;
        $user->save();
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'District stored'
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/store/profile-photo",
     *     tags={"ProfileAPI"},
     *     summary="Profile Photo",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
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
     */
    public function storeProfilePhoto(Request $request)
    {
        $profile = new ProfileService();
        $photoName = $profile->storeProfilePhoto($request);
        if ($photoName) {
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => 'Profile photo stored',
                    'photo_name' => $photoName
                ]
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => [
                'message' => 'Failed to store profile photo',
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/description",
     *     tags={"ProfileAPI"},
     *     summary="Profile description",
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
    public function editDesctiption(Request $request)
    {
        $profile = new ProfileService();
        $profile->editDescription($request);
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Description edited'
            ]
        ]);
    }

    public function userNotifications(Request $request)
    {
        $profile = new ProfileService();
        $profile->userNotifications($request);
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Subscription for notifications upgraded'
            ]
        ]);
    }
}
