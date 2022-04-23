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
    public function index()
    {
        $user = Auth::user();
        return new UserIndexResource($user);
    }

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
     *
     * @OA\Post (
     *     path="/api/settings/update",
     *     tags={"Profile"},
     *     summary="Update Settings",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="age",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="phone_number",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="location",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"admin@admin.com",
     *                     "age":17,
     *                     "phone_number":"999098998",
     *                     "description":"Assalomu aleykum",
     *                     "location":"Xorazm viloyati",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="email", type="string", example="admin@admin.com"),
     *              @OA\Property(property="age", type="integer", example=20),
     *              @OA\Property(property="phone_number", type="string", example="999098998"),
     *              @OA\Property(property="description", type="string", example="Assalomu aleykum"),
     *              @OA\Property(property="location", type="string", example="Xorazm viloyati"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
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

    public function editData()
    {
        $profile = new ProfileService();
        $data = $profile->settingsEdit();
        return response()->json($data);
    }

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
