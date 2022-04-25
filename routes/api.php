<?php

use App\Http\Controllers\API\CategoriesAPIController; // javoxir
use App\Http\Controllers\API\FaqAPIController; // javoxir
use App\Http\Controllers\API\LoginAPIController;
use App\Http\Controllers\API\PerformerAPIController; // javoxir
use App\Http\Controllers\API\ProfileAPIController; // javoxir +
use App\Http\Controllers\API\ResponseAPIController;
use App\Http\Controllers\API\SocialAPIController;
use App\Http\Controllers\API\TaskAPIController; // javoxir
use App\Http\Controllers\API\UpdateAPIController;
use App\Http\Controllers\API\UserAPIController; // javoxir
use App\Http\Controllers\API\SearchAPIController; // javoxir -
use App\Http\Controllers\API\MassmediaAPIController; // javoxir
use App\Http\Controllers\API\ConversationAPIController;
use App\Http\Controllers\API\VoyagerUserAPIController; // javoxir -
use App\Http\Controllers\API\RefillAPIController; // javoxir
use App\Http\Controllers\API\ReportAPIController; // javoxir
use App\Http\Controllers\API\PaynetTransactionAPIController;
use App\Http\Controllers\PortfolioAPIController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('custom.auth:api')->group(function () {
    Route::post('logout', [UserAPIController::class, 'logout']); //end

    Route::post('task/create', [TaskAPIController::class, 'create']); //end

//    Route::any('/{paysys}',function($paysys){
//        (new Goodoneuz\PayUz\PayUz)->driver($paysys)->handle();
//    });

    Route::get('/my-tasks-count', [TaskAPIController::class, 'my_tasks_count']);
    Route::get('/my-tasks', [TaskAPIController::class, 'my_tasks_all']);
    Route::delete('/for_del_new_task/{task}', [TaskAPIController::class, 'deletetask']); //end
    Route::delete('/delete-task/{task}', [SearchAPIController::class, 'delete_task']); //end
    Route::delete('/delete', [UserAPIController::class, 'destroy']); //end

    Route::get('account/verification/email', [LoginAPIController::class, 'send_email_verification']);
    Route::get('account/verification/phone', [LoginAPIController::class, 'send_phone_verification']);
    Route::post('account/verification/phone', [LoginAPIController::class, 'verify_phone']);
    Route::post("account/change/email", [LoginAPIController::class,'change_email']);
    Route::post("account/change/phone", [LoginAPIController::class,'change_phone_number']);

    Route::post("/task/{task}/response", [TaskAPIController::class, 'response_store']);
    Route::post('/select-performer/{response}', [ResponseAPIController::class, 'selectPerformer']);
    Route::post('/task/{task}/complete', [UpdateAPIController::class, 'completed']);
    Route::post('/send-review-user/{task}', [UpdateAPIController::class, 'sendReview']);

    // Profile API
    Route::prefix('/profile')->group(function () {
        // Profile
        Route::get('/', [ProfileAPIController::class, 'index']);
        Route::post('/change-avatar', [ProfileAPIController::class, 'avatar']);

        // Profile Cash
        Route::get('/cash', [ProfileAPIController::class, 'cash']);

        // Profile Settings
        Route::get('/settings', [ProfileAPIController::class, 'editData']);
        Route::post('/settings/update', [ProfileAPIController::class, 'updateData']);
        Route::post('/category/update', [ProfileAPIController::class, 'updateCategory']);
        Route::post('/sessions/clear', [ProfileAPIController::class, 'clearSessions']);
        Route::post('/password/change', [ProfileAPIController::class, 'change_password']);
        Route::get('/notifications/subscription', [ProfileAPIController::class, 'userNotifications']);

        // Profile Delete
        Route::post('/delete', [ProfileAPIController::class, 'deleteUser']);

        // Profile Details
        Route::post('/store/district', [ProfileAPIController::class, 'storeDistrict']);
        Route::post('/store/profile-photo', [ProfileAPIController::class, 'storeProfilePhoto']);
        Route::post('/description', [ProfileAPIController::class, 'editDesctiption']);

        // Profile Portfolio
        Route::delete('/delete/portfolio/{portfolio}', [PortfolioAPIController::class, 'delete']);
        Route::post('/portfolio/create', [PortfolioAPIController::class, 'createPortfolio']);

        // comment
        // testBase
        // portfolio/{portfolio}
    });

});
Route::post('create-task/routing', [TaskAPIController::class,'routing']);

//User Routes
Route::post('login', [UserAPIController::class, 'login']); //end
Route::post('register', [UserAPIController::class, 'register']); //end

Route::post('/reset', [UserAPIController::class, 'reset_submit']);
Route::post('/reset/password', [UserAPIController::class, 'reset_password_save'])->name('user.reset_password_save');
Route::post('/code', [UserAPIController::class, 'reset_code'])->name('user.reset_code');




// FAQ
Route::get('faq', [FaqAPIController::class, 'index']); //end
Route::get('faq/{id}', [FaqAPIController::class, 'questions']); //end

//Tasks
Route::get('task/{task}', [TaskAPIController::class, 'task']); //end
Route::get('tasks-filter', [TaskAPIController::class, 'filter']); //end
Route::get('responses/{task}', [TaskAPIController::class, 'responses']); //end
Route::get('same-tasks/{task}', [TaskAPIController::class, 'same_tasks']); //end
Route::get('tasks-search', [SearchAPIController::class, 'ajax_tasks']); //end
Route::get('search-task', [SearchAPIController::class, 'task_search']); //end
Route::post('ajax-request', [SearchAPIController::class, 'task_response']); //not
Route::get('/detailed-tasks/{task}', [SearchAPIController::class, 'task']); //end

//Categories
Route::get('/categories', [CategoriesAPIController::class, 'index']); //end
Route::get('/categories-parent', [CategoriesAPIController::class, 'parents']); //end 
Route::get('/categories/{id}', [CategoriesAPIController::class, 'show']); //end
Route::get('/category/search', [CategoriesAPIController::class, 'search']); //end

//Performers
Route::get('/performers', [PerformerAPIController::class, 'service']); //end
Route::get('/performers/{performer}', [PerformerAPIController::class, 'performer']); //end

//Portfolio
Route::get('/portfolio_albums/{performer}', [PortfolioAPIController::class, 'index']); //end
Route::get('/portfolio_album/{portfolio}', [PortfolioAPIController::class, 'show']); //end




//Massmedia
Route::get('/press',[MassmediaAPIController::class, 'index']); //end

//Conversation
Route::group(['prefix' => 'admin'], function () {
    // Admin Kerakmas, Kompda kirishadi
    Route::get('/messages/chat/{id}', [ConversationAPIController::class, 'showChat']);
    Route::post('/messages/chat/rate/{message}', [ConversationAPIController::class, 'rating']);
    Route::post('/messages/chat/close/{message}', [ConversationAPIController::class, 'close']);
    Route::post('/messages/chat/{id}', [ConversationAPIController::class, 'send']);
    Route::get("users/activitiy/{user}", [VoyagerUserAPIController::class, "activity"]); // so`rash kerak
    Route::get('/reports', [ReportAPIController::class, 'index']); //end
});

//Refill
Route::get('/ref', [RefillAPIController::class, 'ref']); //end
Route::post('/prepare', [RefillAPIController::class, 'prepare']); //end
Route::post('/complete', [RefillAPIController::class, 'complete']); //end
Route::post('/paynet-transaction', [PaynetTransactionAPIController::class, 'create'])->name('paynet-transaction');



Route::get('login/google/callback',[SocialAPIController::class,'loginWithGoogle']);

Route::get('login/facebook/callback',[SocialAPIController::class,'loginWithFacebook']);

