<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    BlogController,
    PerformerAPIController,
    ProfileAPIController,
    CategoriesAPIController,
    LoginAPIController,
    SessionController,
    SocialAPIController,
    TaskAPIController,
    UpdateAPIController,
    UserAPIController,
    SearchAPIController,
    FaqController
};
use App\Http\Controllers\{NotificationController, vendor\Chatify\Api\MessagesController, VoyagerTaskController};


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
    //Chat
    Route::group(['prefix' => 'chat'], static function () {
        Route::post('/sendMessage', [MessagesController::class, 'send']); // fix
        Route::get('/getContacts', [MessagesController::class, 'getContacts']); // fix
        Route::get('/search', [MessagesController::class, 'search']); // fix
        Route::post('/fetchMessages', [MessagesController::class, 'fetch']); // fix
        Route::post('/makeSeen', [MessagesController::class, 'seen']); // fix
        Route::post('/deleteConversation', [MessagesController::class, 'deleteConversation']); // fix
    });
    //Task Create
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
    //Task Update
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
    Route::post('/read-all-notification', [NotificationController::class, 'read_all_mobile_notification']); // fix

    Route::get('/my-tasks-count', [TaskAPIController::class, 'my_tasks_count']); // fix
    Route::get('/my-tasks', [TaskAPIController::class, 'my_tasks_all']); // fix
    Route::get('/performer-tasks', [TaskAPIController::class, 'performer_tasks']); // fix
    Route::get('/all-tasks', [TaskAPIController::class, 'all_tasks']); // fix
    Route::post('/cancel-task/{task}', [SearchAPIController::class, 'cancelTask']); // fix
    Route::delete('/delete-task/{task}/{user}', [SearchAPIController::class, 'delete_task']); // fix
    Route::post("/task/{task}/response", [TaskAPIController::class, 'response_store']); // fix
    Route::get('/responses/{task}', [TaskAPIController::class, 'responses']); // fix
    Route::get('/complain/types', [TaskAPIController::class, 'complainTypes']); // fix
    Route::post('/task/{task}/complain', [TaskAPIController::class, 'complain']); // fix
    Route::post('/select-performer/{response}', [TaskAPIController::class, 'selectPerformer']); // fix
    Route::post('/task-status-update/{task}', [TaskAPIController::class, 'taskStatusUpdate']); // fix
    Route::post('/task/{task}/complete', [UpdateAPIController::class, 'completed']); // fix
    Route::post('/tasks/{task}/not-complete', [UpdateAPIController::class, 'not_completed']); // fix
    Route::post('/send-review-user/{task}', [UpdateAPIController::class, 'sendReview']); // fix
    Route::post('/give-task', [PerformerAPIController::class, 'give_task']); // fix
    Route::post('/become-performer', [PerformerAPIController::class, 'becomePerformerData']); // fix
    Route::post('/become-performer-phone', [PerformerAPIController::class, 'becomePerformerEmailPhone']); // fix
    Route::post('/become-performer-avatar', [PerformerAPIController::class, 'becomePerformerAvatar']); // fix
    Route::post('/become-performer-category', [PerformerAPIController::class, 'becomePerformerCategory']); // fix
    Route::get('/reviews', [PerformerAPIController::class, 'reviews']); // fix
    Route::post('/task-cancel/{task}', [SearchAPIController::class, 'task_cancel']); // fix

    //Verification
    Route::get('account/verify', [LoginAPIController::class, 'verifyCredentials']); // fix
    Route::post('account/verification/phone', [LoginAPIController::class, 'verify_phone']); // fix
    Route::post('account/verification/email', [LoginAPIController::class, 'verify_email']); // fix

    // Profile API
    Route::prefix('/profile')->group(function () {
        // Profile
        Route::get('/', [ProfileAPIController::class, 'index']); // fix
        Route::get('/portfolios', [ProfileAPIController::class, 'portfolios']); // fix
        Route::post('/portfolio/create', [ProfileAPIController::class, 'portfolioCreate']); // fix
        Route::post('/portfolio/{portfolio}/update', [ProfileAPIController::class, 'portfolioUpdate']); // fix
        Route::delete('/portfolio/{portfolio}/delete', [ProfileAPIController::class, 'portfolioDelete']); // fix
        Route::post('/portfolio/{portfolio}/delete-image', [ProfileAPIController::class, 'deleteImage']); // fix
        Route::get('/reviews', [ProfileAPIController::class, 'reviews']); // fix
        Route::post('/video', [ProfileAPIController::class, 'videoStore']); // fix
        Route::delete('/video/delete', [ProfileAPIController::class, 'videoDelete']); // fix
        Route::get('/self-delete', [ProfileAPIController::class, 'selfDelete']); // fix
        Route::post('/confirmation-self-delete', [ProfileAPIController::class, 'confirmationSelfDelete']); // fix
        Route::get('/balance', [ProfileAPIController::class, 'balance']); // fix
        Route::get('/response-template', [ProfileAPIController::class, 'response_template']); // fix
        Route::post('/response-template/edit/{id}', [ProfileAPIController::class, 'response_template_edit']); // fix
        Route::post('/response-template/create', [ProfileAPIController::class, 'response_template_create']); // fix
        Route::delete('/response-template/delete/{template}', [ProfileAPIController::class, 'response_template_delete']); // fix
        Route::post('/description/edit', [ProfileAPIController::class, 'editDescription']); // fix
        Route::post('/work-experience', [ProfileAPIController::class, 'work_experience']); // fix
        Route::post('/categories-subscribe', [ProfileAPIController::class, 'subscribeToCategory']); // fix
        Route::post('/firebase-token', [NotificationController::class, 'setToken']); // fix
        Route::get('/sessions', [SessionController::class, 'index']); // fix
        Route::post('/clear-sessions', [SessionController::class, 'clearSessions']); // fix
        Route::post('/report-user', [ProfileAPIController::class, 'report']); // fix
        Route::post('/block-user', [ProfileAPIController::class, 'block']); // fix
        Route::get('/block-user-list', [ProfileAPIController::class, 'block_user_list']); // fix
        Route::post('/notification-off', [ProfileAPIController::class, 'notification_off']); // fix
        Route::prefix('/settings')->group(function () {
            Route::get('/', [ProfileAPIController::class, 'editData']); // fix
            Route::post('/update', [ProfileAPIController::class, 'updateData']); // fix
            Route::post('/change-avatar', [ProfileAPIController::class, 'avatar']); // fix
            Route::get('/phone', [ProfileAPIController::class, 'phoneEdit']); // fix
            Route::post('/phone/edit', [ProfileAPIController::class, 'phoneUpdate']); // fix
            Route::post('/password/change', [ProfileAPIController::class, 'change_password']); // fix
            Route::post('/notifications', [ProfileAPIController::class, 'userNotifications']); // fix
        });
    });
});
Route::post('/profile/settings/change-lang', [ProfileAPIController::class, 'changeLanguage']); // fix
Route::get('/profile/{user}', [ProfileAPIController::class, 'userProfile']); // fix
Route::get('/profile/{user}/portfolios', [ProfileAPIController::class, 'userPortfolios']); // fix
Route::get('/profile/{user}/reviews', [ProfileAPIController::class, 'userReviews']); // fix


//Setting
Route::get('/settings/get-all', [FaqController::class, 'get_all']); // fix
Route::get('/settings/{settingKey}', [FaqController::class, 'get_key']); // fix

//User Routes
Route::post('login', [UserAPIController::class, 'login']); // fix
Route::post('register', [UserAPIController::class, 'register']); // fix
Route::post('/reset', [UserAPIController::class, 'reset_submit']); // fix
Route::post('/reset/password', [UserAPIController::class, 'reset_password_save']); // fix
Route::post('/code', [UserAPIController::class, 'reset_code'])->name('user.reset_code'); // fix
Route::get('/support-admin', [UserAPIController::class, "getSupportId"]); // fix

//Blog News
Route::get('/blog-news', [BlogController::class, 'index']); // fix
Route::get('/blog-news/{newsId}', [BlogController::class, 'show']); // fix

//Tasks
Route::get('task/{task}', [TaskAPIController::class, 'task']); // fix
Route::post('user/{user}', [TaskAPIController::class, 'active_task_null']); // fix
Route::get('tasks-filter', [TaskAPIController::class, 'filter']); // fix
Route::get('same-tasks/{task}', [TaskAPIController::class, 'same_tasks']); // fix

//CategoryAPI
Route::get('/categories', [CategoriesAPIController::class, 'index']); // fix
Route::get('/popular-categories', [CategoriesAPIController::class, 'popular']); // fix
Route::get('/categories-parent', [CategoriesAPIController::class, 'parents']); // fix
Route::get('/categories/{id}', [CategoriesAPIController::class, 'show']); // fix
Route::get('/category/search', [CategoriesAPIController::class, 'search']); // fix
Route::get('/all-categories-childs', [CategoriesAPIController::class, 'AllCategoriesChildsId']); // fix

//Performers
Route::get('/performers-filter', [PerformerAPIController::class, 'performer_filter']); // fix
Route::get('/performers-count/{categoryId}', [PerformerAPIController::class, 'performers_count']); // fix
Route::get('/performers-image/{categoryId}', [PerformerAPIController::class, 'performers_image']); // fix

//Social
Route::post('/social-login', [SocialAPIController::class, 'login']); // fix

//FAQ
Route::get('/faq', [FaqController::class, 'index']); // fix
Route::get('/faq/{faqId}', [FaqController::class, 'faq']); // fix

//Test notifications
Route::post('/firebase-notification', [NotificationController::class, 'firebase_notification']);
Route::post('/pusher-notification', [NotificationController::class, 'pusher_notification']);
Route::post('/sms-notification', [NotificationController::class, 'sms_notification']);
Route::post('/email-notification', [NotificationController::class, 'email_notification']);
Route::post('/task-create-notification', [NotificationController::class, 'task_create_notification']);
Route::get('/test-complete-task/{task}', [VoyagerTaskController::class, 'test_complete_task']);
Route::get('/test-delete-task/{task}', [VoyagerTaskController::class, 'test_delete_task']);
Route::get('/test-cancel-task/{task}', [VoyagerTaskController::class, 'test_cancel_task']);
