<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CategoriesAPIController; // javoxir
use App\Http\Controllers\API\CustomFieldAPIController;
use App\Http\Controllers\API\LoginAPIController;
use App\Http\Controllers\API\PerformerAPIController; // javoxir
use App\Http\Controllers\API\ProfileAPIController; // javoxir +
use App\Http\Controllers\API\SessionController;
use App\Http\Controllers\API\SocialAPIController;
use App\Http\Controllers\API\TaskAPIController; // javoxir
use App\Http\Controllers\API\UpdateAPIController;
use App\Http\Controllers\API\UserAPIController; // javoxir
use App\Http\Controllers\API\SearchAPIController; // javoxir -
use App\Http\Controllers\API\RefillAPIController; // javoxir
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortfolioAPIController;
use App\Http\Controllers\vendor\Chatify\Api\MessagesController;


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

    Route::group(['prefix' => 'chat'], function (){
        Route::post('/sendMessage', [MessagesController::class, 'send']); // used
        Route::get('/getContacts', [MessagesController::class, 'getContacts']); // used
        Route::get('/search', [MessagesController::class, 'search']); // used
        Route::post('/fetchMessages', [MessagesController::class, 'fetch']); // used
        Route::post('/makeSeen', [MessagesController::class, 'seen']);
    });
    Route::post('task/create', [TaskAPIController::class, 'create']);
    Route::post('create-task/name', [TaskAPIController::class, 'name']); // used
    Route::post('create-task/custom', [TaskAPIController::class, 'custom']); // used
    Route::post('create-task/remote', [TaskAPIController::class, 'remote']); // used
    Route::post('create-task/address', [TaskAPIController::class, 'address']); // used
    Route::post('create-task/date', [TaskAPIController::class, 'date']); // used
    Route::post('create-task/budget', [TaskAPIController::class, 'budget']); // used
    Route::post('create-task/note', [TaskAPIController::class, 'note']); // used
    Route::post('create-task/images', [TaskAPIController::class, 'uploadImages']); // used
    Route::post('create-task/contacts', [TaskAPIController::class, 'contacts']); // used
    Route::post('create-task/verify', [TaskAPIController::class, 'verify']); // used

    Route::post('update-task/{task}/name', [TaskAPIController::class, 'updateName']); // used
    Route::post('update-task/{task}/custom', [TaskAPIController::class, 'updateCustom']); // used
    Route::post('update-task/{task}/remote', [TaskAPIController::class, 'updateRemote']); // used
    Route::post('update-task/{task}/address', [TaskAPIController::class, 'updateAddress']); // used
    Route::post('update-task/{task}/date', [TaskAPIController::class, 'updateDate']); // used
    Route::post('update-task/{task}/budget', [TaskAPIController::class, 'updateBudget']); // used
    Route::post('update-task/{task}/note', [TaskAPIController::class, 'updateNote']); // used
    Route::post('update-task/{task}/images', [TaskAPIController::class, 'updateUploadImages']); // used
    Route::post('update-task/{task}/contacts', [TaskAPIController::class, 'updateContacts']); // used
    Route::post('update-task/{task}/verify', [TaskAPIController::class, 'updateVerify']); // used

    Route::get('/notifications', [NotificationController::class, 'getNotifications']); //used
    Route::post('/read-notification/{notification}', [NotificationController::class, 'read_notification']); //used

    Route::get('/my-tasks-count', [TaskAPIController::class, 'my_tasks_count']); // used
    Route::get('/my-tasks', [TaskAPIController::class, 'my_tasks_all']); // used
    Route::delete('/for_del_new_task/{task}', [TaskAPIController::class, 'deletetask']); //end +
    Route::delete('/delete-task/{task}', [SearchAPIController::class, 'delete_task']); // used
    Route::delete('/delete', [UserAPIController::class, 'destroy']); //end +

    Route::get('account/verify', [LoginAPIController::class, 'verifyCredentials']); // used
    Route::get('account/verification/email', [LoginAPIController::class, 'send_email_verification']); //end +
    Route::get('account/verification/phone', [LoginAPIController::class, 'send_phone_verification']); //end +
    Route::post('account/verification/phone', [LoginAPIController::class, 'verify_phone']); // used
    Route::post("account/change/email", [LoginAPIController::class, 'change_email']); //end +
    Route::post("account/change/phone", [LoginAPIController::class, 'change_phone_number']); //end +

    Route::post("/task/{task}/response", [TaskAPIController::class, 'response_store']); // used
    Route::get('/responses/{task}', [TaskAPIController::class, 'responses']); // used
    Route::get('/complain/types', [TaskAPIController::class, 'complainTypes']); // used
    Route::post('/task/{task}/complain', [TaskAPIController::class, 'complain']); // used
    Route::post('/select-performer/{response}', [TaskAPIController::class, 'selectPerformer']); // used
    Route::post('/task/{task}/complete', [UpdateAPIController::class, 'completed']); //end +
    Route::post('/send-review-user/{task}', [UpdateAPIController::class, 'sendReview']); //end +
    Route::get('/change-task/{task}', [TaskAPIController::class, 'getTask']); //end
    Route::put('/change-task/{task}', [TaskAPIController::class, 'changeTask']); //end -
    Route::post('give-task', [PerformerAPIController::class, 'give_task']); // used
    Route::post('/become-performer', [PerformerAPIController::class, 'becomePerformerData']); // used
    Route::post('/become-performer-phone', [PerformerAPIController::class, 'becomePerformerEmailPhone']); // used
    Route::post('/become-performer-avatar', [PerformerAPIController::class, 'becomePerformerAvatar']); //end +
    Route::post('/become-performer-category', [PerformerAPIController::class, 'becomePerformerCategory']); // used
    Route::get('/reviews', [PerformerAPIController::class, 'reviews']); //end

    Route::get('/custom-field-by-category/{category}', [CustomFieldAPIController::class, 'getByCategoryId']); //end -
    Route::get('/custom-field-values-by-task/{task}', [CustomFieldAPIController::class, 'getByTaskId']); //end -

    // Profile API
    Route::prefix('/profile')->group(function () {
        // Profile
        Route::get('/', [ProfileAPIController::class, 'index']); // used
        Route::get('/portfolios', [ProfileAPIController::class, 'portfolios']); // used
        Route::post('/portfolio/create', [ProfileAPIController::class, 'portfolioCreate']); // used
        Route::post('/portfolio/{portfolio}/update', [ProfileAPIController::class, 'portfolioUpdate']); // used
        Route::post('/portfolio/{portfolio}/delete', [ProfileAPIController::class, 'portfolioDelete']); // used
        Route::get('/reviews', [ProfileAPIController::class, 'reviews']); // used
        Route::post('/video', [ProfileAPIController::class, 'videoStore']); // used
        Route::delete('/video/delete', [ProfileAPIController::class, 'videoDelete']); //end
        Route::get('/balance', [ProfileAPIController::class, 'balance']); // used
        Route::get('/description', [ProfileAPIController::class, 'description']); //end +
        Route::post('/description/edit', [ProfileAPIController::class, 'editDesctiption']); // used
        Route::post('/payment', [ProfileAPIController::class, 'payment']); //end
        Route::post('/categories-subscribe', [ProfileAPIController::class, 'subscribeToCategory']); // used
        Route::post('/firebase-token', [NotificationController::class, 'setToken']); //used
        Route::get('/sessions', [SessionController::class, 'index']); // used
        Route::prefix('/settings')->group(function () {
            Route::get('/', [ProfileAPIController::class, 'editData']); // used
            Route::post('/update', [ProfileAPIController::class, 'updateData']); // used
            Route::post('/change-avatar', [ProfileAPIController::class, 'avatar']); //end -
            Route::get('/phone', [ProfileAPIController::class, 'phoneEdit']); //end +
            Route::post('/phone/edit', [ProfileAPIController::class, 'phoneUpdate']); // used
            Route::post('/phone/verify', [ProfileAPIController::class, 'phoneVerify']); //end
            Route::post('/password/change', [ProfileAPIController::class, 'change_password']); // used
            Route::post('/notifications', [ProfileAPIController::class, 'userNotifications']); // used
            Route::post('/change-lang', [ProfileAPIController::class, 'changeLanguage']); //end
        });
    });
});
Route::get('/profile/{user}', [ProfileAPIController::class, 'userProfile']); // used
Route::get('/profile/{user}/portfolios', [ProfileAPIController::class, 'userPortfolios']); // used
Route::get('/profile/{user}/reviews', [ProfileAPIController::class, 'userReviews']); // used


//User Routes
Route::post('login', [UserAPIController::class, 'login']); //used
Route::post('register', [UserAPIController::class, 'register']); //used

Route::post('/reset', [UserAPIController::class, 'reset_submit']); //end +
Route::post('/reset/password', [UserAPIController::class, 'reset_password_save'])->name('user.reset_password_save'); //end +
Route::post('/code', [UserAPIController::class, 'reset_code'])->name('user.reset_code'); //end +

//News
Route::get('/blog-news', [BlogController::class, 'index']); // used

//Tasks
Route::get('task/{task}', [TaskAPIController::class, 'task']); // used
Route::get('tasks-filter', [TaskAPIController::class, 'filter']); // used
Route::get('same-tasks/{task}', [TaskAPIController::class, 'same_tasks']); // used
Route::get('tasks-search', [SearchAPIController::class, 'ajax_tasks']); //end
Route::get('search-task', [SearchAPIController::class, 'task_search']); //end
Route::post('ajax-request', [SearchAPIController::class, 'task_response']); //not
Route::get('/detailed-tasks/{task}', [SearchAPIController::class, 'task']); //end

//Categories

Route::get('/categories', [CategoriesAPIController::class, 'index']); //end -
Route::get('/popular-categories', [CategoriesAPIController::class, 'popular']); // used
Route::get('/categories-parent', [CategoriesAPIController::class, 'parents']); //used
Route::get('/categories/{id}', [CategoriesAPIController::class, 'show']); //end +
Route::get('/category/search', [CategoriesAPIController::class, 'search']); // used

//Performers
Route::get('/performers', [PerformerAPIController::class, 'service']); // used
Route::get('/performers/{performer}', [PerformerAPIController::class, 'performer']); //end +

//Portfolio
Route::get('/portfolio_albums/{performer}', [PortfolioAPIController::class, 'index']); //end +
Route::get('/portfolio_album/{portfolio}', [PortfolioAPIController::class, 'show']); //end +

//Refill
Route::get('/ref', [RefillAPIController::class, 'ref']); //end

#Social
Route::post('/social-login', [SocialAPIController::class, 'login']); // used

