<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    BlogController, PerformerAPIController, ProfileAPIController,
    CategoriesAPIController, LoginAPIController, SessionController,
    SocialAPIController, TaskAPIController, UpdateAPIController,
    UserAPIController, SearchAPIController,FaqController
};
use App\Http\Controllers\{
    NotificationController,
    vendor\Chatify\Api\MessagesController
};


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

Route::middleware(['custom.auth:api', 'is_user_active'])->group(function () {
    Route::post('logout', [UserAPIController::class, 'logout']); // fix

    Route::group(['prefix' => 'chat'], function (){

        Route::post('/sendMessage', [MessagesController::class, 'send']); // fix
        Route::get('/getContacts', [MessagesController::class, 'getContacts']); // fix
        Route::get('/search', [MessagesController::class, 'search']); // fix
        Route::post('/fetchMessages', [MessagesController::class, 'fetch']); // fix
        Route::post('/makeSeen', [MessagesController::class, 'seen']); // fix
        Route::post('/deleteConversation', [MessagesController::class, 'deleteConversation']); // fix

    });
    Route::post('create-task/name', [TaskAPIController::class, 'name']); // fix
    Route::post('create-task/custom', [TaskAPIController::class, 'custom']); // fix
    Route::post('create-task/remote', [TaskAPIController::class, 'remote']); // fix
    Route::post('create-task/address', [TaskAPIController::class, 'address']); // fix
    Route::post('create-task/date', [TaskAPIController::class, 'date']); // fix
    Route::post('create-task/budget', [TaskAPIController::class, 'budget']); // fix
    Route::post('create-task/note', [TaskAPIController::class, 'note']); // fix
    Route::post('create-task/images', [TaskAPIController::class, 'uploadImages']); // fix
    Route::post('create-task/contacts', [TaskAPIController::class, 'contacts']); // fix
    Route::post('create-task/verify', [TaskAPIController::class, 'verify']); // fix

    Route::post('update-task/{task}/name', [TaskAPIController::class, 'updateName']); // fix
    Route::post('update-task/{task}/custom', [TaskAPIController::class, 'updateCustom']); // fix
    Route::post('update-task/{task}/remote', [TaskAPIController::class, 'updateRemote']); // fix
    Route::post('update-task/{task}/address', [TaskAPIController::class, 'updateAddress']); // fix
    Route::post('update-task/{task}/date', [TaskAPIController::class, 'updateDate']); // fix
    Route::post('update-task/{task}/budget', [TaskAPIController::class, 'updateBudget']); // fix
    Route::post('update-task/{task}/note', [TaskAPIController::class, 'updateNote']); // fix
    Route::post('update-task/{task}/images', [TaskAPIController::class, 'updateUploadImages']); // fix
    Route::post('update-task/{task}/contacts', [TaskAPIController::class, 'updateContacts']); // fix
    Route::post('update-task/{task}/verify', [TaskAPIController::class, 'updateVerify']); // fix
    Route::post('update-task/{task}/delete-image', [TaskAPIController::class, 'deleteImage']); // fix

    Route::get('/notifications', [NotificationController::class, 'getNotifications']); // fix
    Route::get('/count/notifications', [NotificationController::class, 'count']); // fix
    Route::post('/read-notification/{notification}', [NotificationController::class, 'read_notification']); // fix

    Route::get('/my-tasks-count', [TaskAPIController::class, 'my_tasks_count']); // used
    Route::get('/my-tasks', [TaskAPIController::class, 'my_tasks_all']); // used
    Route::get('/performer-tasks', [TaskAPIController::class, 'performer_tasks']); // used
    Route::post('/cancel-task/{task}', [SearchAPIController::class, 'cancelTask']); // should use
    Route::delete('/delete-task/{task}', [SearchAPIController::class, 'delete_task']); // used

    Route::get('account/verify', [LoginAPIController::class, 'verifyCredentials']); // used
    Route::post('account/verification/phone', [LoginAPIController::class, 'verify_phone']); // used

    Route::post("/task/{task}/response", [TaskAPIController::class, 'response_store']); // used
    Route::get('/responses/{task}', [TaskAPIController::class, 'responses']); // used
    Route::get('/complain/types', [TaskAPIController::class, 'complainTypes']); // used
    Route::post('/task/{task}/complain', [TaskAPIController::class, 'complain']); // used
    Route::post('/select-performer/{response}', [TaskAPIController::class, 'selectPerformer']); // used
    Route::post('/task-status-update/{task}', [TaskAPIController::class, 'taskStatusUpdate']); // used
    Route::post('/task/{task}/complete', [UpdateAPIController::class, 'completed']); //end +
    Route::post('/tasks/{task}/not-complete', [UpdateAPIController::class, 'not_completed'])->name('update.not_completed'); // used
    Route::post('/send-review-user/{task}', [UpdateAPIController::class, 'sendReview']); // used
    Route::post('/give-task', [PerformerAPIController::class, 'give_task']); // used
    Route::post('/become-performer', [PerformerAPIController::class, 'becomePerformerData']); // used
    Route::post('/become-performer-phone', [PerformerAPIController::class, 'becomePerformerEmailPhone']); // used
    Route::post('/become-performer-avatar', [PerformerAPIController::class, 'becomePerformerAvatar']); //end +
    Route::post('/become-performer-category', [PerformerAPIController::class, 'becomePerformerCategory']); // used
    Route::get('/reviews', [PerformerAPIController::class, 'reviews']); //end
    Route::get('/settings/get-all', [FaqController::class, 'get_all']);
    Route::get('/settings/{key}', [FaqController::class, 'get_key']);

    // Profile API
    Route::prefix('/profile')->group(function () {
        // Profile
        Route::get('/', [ProfileAPIController::class, 'index']); // used
        Route::get('/portfolios', [ProfileAPIController::class, 'portfolios']); // used
        Route::post('/portfolio/create', [ProfileAPIController::class, 'portfolioCreate']); // used
        Route::post('/portfolio/{portfolio}/update', [ProfileAPIController::class, 'portfolioUpdate']); // used
        Route::delete('/portfolio/{portfolio}/delete', [ProfileAPIController::class, 'portfolioDelete']); // used
        Route::post('/portfolio/{portfolio}/delete-image', [ProfileAPIController::class, 'deleteImage']);
        Route::get('/reviews', [ProfileAPIController::class, 'reviews']); // used
        Route::post('/video', [ProfileAPIController::class, 'videoStore']); // used
        Route::delete('/video/delete', [ProfileAPIController::class, 'videoDelete']); //end
        Route::get('/self-delete', [ProfileAPIController::class, 'selfDelete']); //end
        Route::post('/confirmation-self-delete', [ProfileAPIController::class, 'confirmationSelfDelete']); //end
        Route::get('/balance', [ProfileAPIController::class, 'balance']); // used
        Route::post('/description/edit', [ProfileAPIController::class, 'editDesctiption']); // used
        Route::post('/categories-subscribe', [ProfileAPIController::class, 'subscribeToCategory']); // used
        Route::post('/firebase-token', [NotificationController::class, 'setToken']); //used
        Route::get('/sessions', [SessionController::class, 'index']); // used
        Route::post('/clear-sessions', [SessionController::class, 'clearSessions']);
        Route::post('/report-user', [ProfileAPIController::class, 'report']);
        Route::post('/block-user', [ProfileAPIController::class, 'block']);
        Route::prefix('/settings')->group(function () {
            Route::get('/', [ProfileAPIController::class, 'editData']); // used
            Route::post('/update', [ProfileAPIController::class, 'updateData']); // used
            Route::post('/change-avatar', [ProfileAPIController::class, 'avatar']); //end -
            Route::get('/phone', [ProfileAPIController::class, 'phoneEdit']); //end +
            Route::post('/phone/edit', [ProfileAPIController::class, 'phoneUpdate']); // used
            Route::post('/password/change', [ProfileAPIController::class, 'change_password']); // used
            Route::post('/notifications', [ProfileAPIController::class, 'userNotifications']); // used

        });
    });
});
Route::post('/profile/settings/change-lang', [ProfileAPIController::class, 'changeLanguage']); // used
Route::get('/profile/{user}', [ProfileAPIController::class, 'userProfile']); // used
Route::get('/profile/{user}/portfolios', [ProfileAPIController::class, 'userPortfolios']); // used
Route::get('/profile/{user}/reviews', [ProfileAPIController::class, 'userReviews']); // used


//User Routes
Route::post('login', [UserAPIController::class, 'login']); //used
Route::post('register', [UserAPIController::class, 'register']); //used

Route::post('/reset', [UserAPIController::class, 'reset_submit']); //end +
Route::post('/reset/password', [UserAPIController::class, 'reset_password_save'])->name('user.reset_password_save'); //end +
Route::post('/code', [UserAPIController::class, 'reset_code'])->name('user.reset_code'); //end +

Route::get('/support-admin', [UserAPIController::class, "getSupportId"]);

//News
Route::get('/blog-news', [BlogController::class, 'index']); // used
Route::get('/blog-news/{blogNew}', [BlogController::class, 'show']); // used

//Tasks
Route::get('task/{task}', [TaskAPIController::class, 'task']); // used
Route::post('user/{user}', [TaskAPIController::class, 'active_task_null']); // used
Route::get('tasks-filter', [TaskAPIController::class, 'filter']); // used
Route::get('same-tasks/{task}', [TaskAPIController::class, 'same_tasks']); // used

//Categories
Route::get('/categories', [CategoriesAPIController::class, 'index']); //end -
Route::get('/popular-categories', [CategoriesAPIController::class, 'popular']); // used
Route::get('/categories-parent', [CategoriesAPIController::class, 'parents']); //used
Route::get('/categories/{id}', [CategoriesAPIController::class, 'show']); //end +
Route::get('/category/search', [CategoriesAPIController::class, 'search']); // used

//Performers
Route::get('/performers', [PerformerAPIController::class, 'service']); // used
Route::get('/performers-filter', [PerformerAPIController::class, 'performer_filter']); // used
Route::get('/performers-count/{category_id}', [PerformerAPIController::class, 'performers_count']);

#Social
Route::post('/social-login', [SocialAPIController::class, 'login']); // used

#faq
Route::get('/faq', [FaqController::class, 'index']);

