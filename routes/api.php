<?php

use App\Http\Controllers\API\CategoriesAPIController; // javoxir
use App\Http\Controllers\API\CustomFieldAPIController;
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
    Route::post('logout', [UserAPIController::class, 'logout']); //end +

    Route::post('task/create', [TaskAPIController::class, 'create']); //end -
    Route::post('create-task/get-fields', [TaskAPIController::class,'getFields']); //end -
    Route::post('create-task/routing', [TaskAPIController::class,'routing']); //end -
//    Route::any('/{paysys}',function($paysys){
//        (new Goodoneuz\PayUz\PayUz)->driver($paysys)->handle();
//    });

    Route::get('/my-tasks-count', [TaskAPIController::class, 'my_tasks_count']); //end +
    Route::get('/my-tasks', [TaskAPIController::class, 'my_tasks_all']); //end +
    Route::delete('/for_del_new_task/{task}', [TaskAPIController::class, 'deletetask']); //end +
    Route::delete('/delete-task/{task}', [SearchAPIController::class, 'delete_task']); //end +
    Route::delete('/delete', [UserAPIController::class, 'destroy']); //end +

    Route::get('account/verification/email', [LoginAPIController::class, 'send_email_verification']); //end +
    Route::get('account/verification/phone', [LoginAPIController::class, 'send_phone_verification']); //end +
    Route::post('account/verification/phone', [LoginAPIController::class, 'verify_phone']); //end +
    Route::post("account/change/email", [LoginAPIController::class,'change_email']); //end +
    Route::post("account/change/phone", [LoginAPIController::class,'change_phone_number']); //end +

    Route::post("/task/{task}/response", [TaskAPIController::class, 'response_store']); //end +
    Route::post('/select-performer/{response}', [ResponseAPIController::class, 'selectPerformer']); //end -
    Route::post('/task/{task}/complete', [UpdateAPIController::class, 'completed']); //end +
    Route::post('/send-review-user/{task}', [UpdateAPIController::class, 'sendReview']); //end +
    Route::put('/change-task/{task}', [TaskAPIController::class, 'changeTask']); //end -
    Route::post('/become-performer', [PerformerAPIController::class,'becomePerformerData']); //end +
    Route::post('/become-performer-phone', [PerformerAPIController::class,'becomePerformerEmailPhone']); //end +
    Route::post('/become-performer-avatar', [PerformerAPIController::class,'becomePerformerAvatar']); //end +
    Route::post('/become-performer-category', [PerformerAPIController::class,'becomePerformerCategory']); //end +
    Route::get('/reviews', [PerformerAPIController::class, 'reviews']);

    Route::get('/custom-field-by-category/{category}', [CustomFieldAPIController::class, 'getByCategoryId']); //end -
    Route::get('/custom-field-values-by-task/{task}', [CustomFieldAPIController::class, 'getByTaskId']); //end -

    // Profile API
    Route::prefix('/profile')->group(function () {
        // Profile
        Route::get('/', [ProfileAPIController::class, 'index']); //end +
        Route::get('/portfolios', [ProfileAPIController::class, 'portfolios']); //end
        Route::post('/portfolio/create', [ProfileAPIController::class, 'portfolioCreate']);
        Route::post('/portfolio/{portfolio}/update', [ProfileAPIController::class, 'portfolioUpdate']);
        Route::post('/portfolio/{portfolio}/delete', [ProfileAPIController::class, 'portfolioDelete']);
        Route::get('/reviews', [ProfileAPIController::class, 'reviews']); //end
        Route::get('/balance', [ProfileAPIController::class, 'balance']); //end
        Route::get('/description', [ProfileAPIController::class, 'description']); //end +
        Route::post('/description/edit', [ProfileAPIController::class, 'editDesctiption']); //end +
        Route::post('/payment', [ProfileAPIController::class, 'payment']);

        Route::prefix('/settings')->group(function () {
            Route::get('/', [ProfileAPIController::class, 'editData']); //end +
            Route::post('/update', [ProfileAPIController::class, 'updateData']); //end +
            Route::post('/change-avatar', [ProfileAPIController::class, 'avatar']); //end -
            Route::get('/phone', [ProfileAPIController::class, 'phoneEdit']); //end +
            Route::post('/phone/edit', [ProfileAPIController::class, 'phoneUpdate']); //end +
            Route::post('/password/change', [ProfileAPIController::class, 'change_password']); //end +
            Route::post('/notifications', [ProfileAPIController::class, 'userNotifications']); //end +
        });
    });
});
Route::get('/profile/{id}', [ProfileAPIController::class, 'userProfile']);
Route::get('/profile/{user}/portfolios', [ProfileAPIController::class, 'userPortfolios']);
Route::get('/profile/{user}/reviews', [ProfileAPIController::class, 'userReviews']);


//User Routes
Route::post('login', [UserAPIController::class, 'login']); //end +
Route::post('register', [UserAPIController::class, 'register']); //end +

Route::post('/reset', [UserAPIController::class, 'reset_submit']); //end +
Route::post('/reset/password', [UserAPIController::class, 'reset_password_save'])->name('user.reset_password_save'); //end +
Route::post('/code', [UserAPIController::class, 'reset_code'])->name('user.reset_code'); //end +




// FAQ
Route::get('faq', [FaqAPIController::class, 'index']); //end +
Route::get('faq/{id}', [FaqAPIController::class, 'questions']); //end +

//Tasks
Route::get('task/{task}', [TaskAPIController::class, 'task']); //end +
Route::get('tasks-filter', [TaskAPIController::class, 'filter']); //end +
Route::get('responses/{task}', [TaskAPIController::class, 'responses']); //end +
Route::get('same-tasks/{task}', [TaskAPIController::class, 'same_tasks']); //end +
Route::get('tasks-search', [SearchAPIController::class, 'ajax_tasks']); //end
Route::get('search-task', [SearchAPIController::class, 'task_search']); //end
Route::post('ajax-request', [SearchAPIController::class, 'task_response']); //not
Route::get('/detailed-tasks/{task}', [SearchAPIController::class, 'task']); //end

//Categories

Route::get('/categories', [CategoriesAPIController::class, 'index']); //end -
Route::get('/categories-parent', [CategoriesAPIController::class, 'parents']); //end +
Route::get('/categories/{id}', [CategoriesAPIController::class, 'show']); //end +
Route::get('/category/search', [CategoriesAPIController::class, 'search']); //end +

//Performers
Route::get('/performers', [PerformerAPIController::class, 'service']); //end +
Route::get('/performers/{performer}', [PerformerAPIController::class, 'performer']); //end +

//Portfolio
Route::get('/portfolio_albums/{performer}', [PortfolioAPIController::class, 'index']); //end +
Route::get('/portfolio_album/{portfolio}', [PortfolioAPIController::class, 'show']); //end +




//Massmedia
Route::get('/press',[MassmediaAPIController::class, 'index']); //end +

//Conversation
Route::group(['prefix' => 'admin'], function () {
    // Admin Kerakmas, Kompda kirishadi
    Route::get('/messages/chat/{id}', [ConversationAPIController::class, 'showChat']);
    Route::post('/messages/chat/rate/{message}', [ConversationAPIController::class, 'rating']);
    Route::post('/messages/chat/close/{message}', [ConversationAPIController::class, 'close']);
    Route::post('/messages/chat/{id}', [ConversationAPIController::class, 'send']);
    Route::get("users/activitiy/{user}", [VoyagerUserAPIController::class, "activity"]);
    Route::get('/reports', [ReportAPIController::class, 'index']); //end
});

//Refill
Route::get('/ref', [RefillAPIController::class, 'ref']); //end
Route::post('/prepare', [RefillAPIController::class, 'prepare']); //end
Route::post('/complete', [RefillAPIController::class, 'complete']); //end
Route::post('/paynet-transaction', [PaynetTransactionAPIController::class, 'create'])->name('paynet-transaction');



Route::post('login/google/callback',[SocialAPIController::class,'loginWithGoogle']);

Route::post('login/callback',[SocialAPIController::class,'loginWithFacebook']); //end

