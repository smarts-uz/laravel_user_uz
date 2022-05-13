<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ClickuzController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaynetController;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdateDataRequest;
use App\Http\Resources\PortfolioIndexResource;
use App\Http\Resources\PortfolioResource;
use App\Http\Resources\ReviewIndexResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserIndexResource;
use App\Models\All_transaction;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Session;
use App\Models\Task;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\Profile\ProfileService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
     *     path="/api/profile",
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
    public function portfolios()
    {
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
    public function portfolioCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }
        $user = auth()->user();
        $data = $validator->validated();
        $data['user_id'] = $user->id;
        if ($request->hasFile('images')) {
            $image = [];
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name.'/'.$data['comment'].'/'.time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path().'/Portfolio/'.$user->name.'/'.$data['comment'].'/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        $portfolio = Portfolio::create($data);
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
    public function portfolioDelete(Portfolio $portfolio)
    {
        portfolioGuard($portfolio);
        $portfolio->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'message' =>'Portfolio deleted'
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/create",
     *     tags={"Profile"},
     *     summary="Portfolio Update",
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
    public function portfolioUpdate(Request $request, Portfolio $portfolio)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }
        $user = auth()->user();
        $data = $validator->validated();
        $data['user_id'] = $user->id;
        if ($request->has('images')) {
            $image = [];
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name.'/'.$data['comment'].'/'.time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path().'Portfolio/'.$user->name.'/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        $portfolio->update($data);
        $portfolio->save();
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
    public function videoStore(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'link' => 'required|url'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Send valid youtube link'
            ]);
        }
        $validated = $validator->validated();
        $link = $validated['link'];
        if (!str_starts_with($link, 'https://www.youtube.com/')) {
            return response()->json([
                'success' => false,
                'message' => 'Send valid youtube link'
            ]);
        }
        $user->youtube_link = str_replace('watch?v=','embed/',$request->youtube_link);;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Youtube link updated'
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
    public function reviews(Request $request)
    {
        $user = auth()->user();
        if ($request->get('performer') == 1) {
            $data = Review::query()->where(['user_id' => $user->id])
                ->whereHas('task', function (Builder $q) use ($user) {
                    $q->where(['performer_id' => $user->id]);
                })->get();
        } else {
            $data = Review::query()->where(['user_id' => $user->id])
                ->whereHas('task', function (Builder $q) use ($user) {
                    $q->where(['user_id' => $user->id]);
                })->get();
        }
        return response()->json([
            'success' => true,
            'data' => ReviewIndexResource::collection($data)
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
    public function balance()
    {
        $user = auth()->user()->load('transactions');
        if (WalletBalance::query()->where('user_id', $user->id)->first() != null)
            $balance = WalletBalance::query()->where('user_id', $user->id)->first()->balance;
        else
            $balance = 0;
        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $balance,
                'transactions' => All_transaction::query()->where(['user_id' => $user->id])->paginate(15)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/description",
     *     tags={"Profile"},
     *     summary="Profile description",
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
    public function description()
    {
        $user = auth()->user();
        return  response()->json([
            'success' => true,
            'data' => $user->description
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
    public function phoneEdit()
    {
        $user = auth()->user();
        return response()->json([
            'data' => [
                'phone_number' => $user->phone_number
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
    public function phoneUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => $validator->errors(),
            ]);
        }
        $userPhone = User::query()->where(['phone_number' => $request->get('phone_number')])->first();
        $user = auth()->user();
        if ($userPhone->id != $user->id) {
            return response()->json([
                'success' => false,
                'data' => [
                    'message' => 'Phone number already exists'
                ]
            ]);
        }
        $user->phone_number = $request->get('phone_number');
        $user->is_phone_number_verified = 0;
        $user->save();
        return response()->json([
            'success' => true,
            'data' => [
                'phone_number' => 'Phone number updated successfully'
            ]
        ]);

    }

    public function payment(Request $request)
    {
        $payment = $request->get("paymethod");
        $request['user_id'] = auth()->user()->id;
        switch($payment) {
            case 'Click':
                $payment = new ClickuzController();
                return $payment->pay($request);
            case 'PayMe':
                $tr = new All_transaction();
                $tr->user_id = Auth::id();
                $tr->amount = $request->get("amount");
                $tr->method = $tr::DRIVER_PAYME;
                $tr->state = $tr::STATE_WAITING_PAY;
                $tr->save();
                return redirect('https://checkout.paycom.uz')->withInput([
                    'merchant' => config('paycom.merchant_id'),
                    'amount' => $tr->amount * 100,
                    'order_id' => $tr->id
                ]);
            case 'Paynet':
                return PaynetController::pay($request);
        }
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
     *     path="/api/profile/settings/change-avatar",
     *     tags={"Profile Settings"},
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


    /**
     * @OA\Get(
     *     path="/api/profile/settings",
     *     tags={"Profile Settings"},
     *     summary="Profile Settings",
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
     *     tags={"Profile Settings"},
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
    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'gender' => 'required',
            'location' => 'nullable',
            'born_date' => 'required|date',
            'age' => 'required',
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ]);
        }
        $validated = $validator->validated();
        if ($validated['email'] != auth()->user()->email) {
            $validated['is_email_verified'] = 0;
            $validated['email_old'] = auth()->user()->email;
        }
        $user = auth()->user();
        $user->update($validated);
        $user->save();
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Settings updated successfully'
            ]
        ], 201);
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
    public function editData()
    {
        $user = auth()->user();
        $data = [
            'name' => $user->name,
            'last_name' => $user->last_name,
            'avatar' => $user->avatar,
            'location' => $user->location,
            'date_of_birth' => $user->born_date,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'gender' => $user->gender,
        ];
        return response()->json([
            'data' => $data
        ]);
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
        return new UserIndexResource($user);
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
    public function editDesctiption(Request $request)
    {
        $profile = new ProfileService();
        $profile->editDescription($request);
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Description edited',
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
    public function userNotifications(Request $request)
    {
        $notification = $request->get('notification');
        $user = auth()->user();
        if ($notification == 1) {
            $user->system_notification = 1;
            $user->news_notification = 1;
            $message = 'Notifications turned on';
        } elseif ($notification == 0) {
            $user->system_notification = 0;
            $user->news_notification = 0;
            $message = 'Notifications turned off';
        } else {
            return response()->json([
                'success' => false,
                'data' => [
                    'message' => 'Send 1 to turn on notifications. Send 0 to turn off notifications'
                ]
            ]);
        }
        $user->save();
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
    public function userProfile($id)
    {
        $user = User::query()->find($id);
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
    public function userPortfolios(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => PortfolioIndexResource::collection(Portfolio::query()->where(['user_id' => $user->id])->get())
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
    public function userReviews(Request $request, User $user)
    {
        if ($request->get('performer') == 1) {
            $data = Review::query()->where(['user_id' => $user->id])
                ->whereHas('task', function (Builder $q) use ($user) {
                    $q->where(['performer_id' => $user->id]);
                })->get();
        } else {
            $data = Review::query()->where(['user_id' => $user->id])
                ->whereHas('task', function (Builder $q) use ($user) {
                    $q->where(['user_id' => $user->id]);
                })->get();
        }
        return response()->json([
            'success' => true,
            'data' => ReviewIndexResource::collection($data)
        ]);
    }

    public function youtube_link(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $user->youtube_link = str_replace('watch?v=','embed/',$request->youtube_link);
        $user->save();
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function youtube_link_delete(){
        $user = User::find(auth()->user()->id);
        $user->youtube_link = null;
        $user->save();
        return response()->json([
            'success' => false,
            'message' => 'Yuklanmadi'
        ]);
    }
}
