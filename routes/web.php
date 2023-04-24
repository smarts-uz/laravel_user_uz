<?php

use App\Http\Controllers\CustomFieldController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Task\ResponseController;
use App\Http\Controllers\Task\UpdateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTransactionHistory;
use App\Http\Controllers\vendor\Chatify\MessagesController;
use App\Http\Controllers\VoyagerTaskController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\PerformersController;
use App\Http\Controllers\Task\SearchTaskController;
use App\Http\Controllers\admin\VoyagerUserController;
use App\Http\Controllers\Task\CreateController;
use TCG\Voyager\Facades\Voyager;
use Teamprodev\LaravelPayment\PayUz;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/debug-sentry', function () {
    Log::channel('api')->info('api ishlamadi');
});
#region performers
Route::get('/for_del_new_task/{task}', [CreateController::class, 'deleteTask']);
Route::post('del-notif', [PerformersController::class, 'del_all_notif']);
Route::get('perf-ajax/{id}', [PerformersController::class, 'perf_ajax'])->name('perf.ajax');
Route::get('active-performers', [PerformersController::class, 'ajaxAP'])->name('performers.active_performers');
Route::post('give-task', [PerformersController::class, 'give_task']);
Route::get('/performers_portfolio/{portfolio}',[PerformersController::class,'performers_portfolio'])->name('performers.performers_portfolio');
Route::group(['prefix' => 'performers'], function () {
    Route::get('/', [PerformersController::class, 'service'])->name('performers.service');
    Route::get('/{user}', [PerformersController::class, 'performer'])->name('performers.performer');
    Route::get('performer/list', [PerformersController::class, 'getPerformers'])->name('performers.list');
});
#endregion

#region chat
Route::group(['prefix' => 'chat'], function (){
    Route::get('/getContacts', [MessagesController::class, 'getContacts'])->name('contacts.get');
    Route::get('/search', [MessagesController::class, 'search'])->name('search');
    Route::post('/favorites', [MessagesController::class, 'getFavorites'])->name('favorites');
    Route::post('/idInfo', [MessagesController::class,'idFetchData']);
    Route::post('/shared', [MessagesController::class, 'sharedPhotos'])->name('shared');
    Route::post('/fetchMessages', [MessagesController::class, 'fetch'])->name('fetch.messages');
    Route::post('/sendMessage', [MessagesController::class, 'send'])->name('send.message');
    Route::post('/makeSeen', [MessagesController::class, 'seen'])->name('messages.seen');
    Route::post('/updateContacts', [MessagesController::class, 'updateContactItem'])->name('contacts.update');
    Route::post('/pusher/auth', [MessagesController::class, 'pusherAuth']);
});
Route::post('/request',[ReportController::class,'request'])->name('request');
Route::group(['prefix' => 'admin'], static function () {
    Voyager::routes();
    Route::group(['middleware' => 'auth'], static function () {
        Route::get('report', [ReportController::class, "index"])->name("index");
        Route::post('report/get', [ReportController::class, "report"])->name("report");
        Route::post('report/get/child', [ReportController::class, "report_sub"])->name("report_sub");
        Route::get('report/{id}', [ReportController::class, "index_sub"])->name("index_sub");
        Route::get('users/activity/{user}', [VoyagerUserController::class, "activity"])->name("voyagerUser.activity");
        Route::get('tasks/cancel/{task}', [VoyagerTaskController::class, "cancelTask"])->name("voyagerTask.cancel");
        Route::get('/resetPassword/{user}',[VoyagerUserController::class,'resetPassword'])->name('voyager.reset.password');
        Route::post('/resetPassword/store/{user}',[VoyagerUserController::class,'resetPassword_store'])->name('voyager.reset.password.store');
        Route::post('/custom-fields/store',[CustomFieldController::class,'store'])->name('voyager.custom-fields.store');
        Route::put('/custom-fields/{id}/update',[CustomFieldController::class,'update'])->name('voyager.custom-fields.update');
        Route::get('/info/{user}',[Controller::class,'user_info'])->name('user.info');
        Route::post('/users/store',[UserController::class,'store'])->name('voyager.users.store');
    });
});
#endregion

#region tasks
Route::group(['middleware' => 'auth'], function () {
    Route::get('/my-tasks', [Controller::class, 'my_tasks'])->name('searchTask.mytasks');
});
Route::get('/completed-task-names', [SearchTaskController::class, 'taskNames'])->name('search.task_name');
Route::get('task-search', [SearchTaskController::class, 'search_new'])->name('searchTask.task_search');
Route::post('tasks-search', [SearchTaskController::class, 'search_new2'])->name('searchTask.ajax_tasks');
Route::post('ajax-request', [SearchTaskController::class, 'task_response']);
Route::delete('delete-task/{task}', [SearchTaskController::class, 'delete_task'])->name('searchTask.delete_task');
Route::get('delete-task/{task}', [SearchTaskController::class, 'delete_task'])->name('searchTask.delete_task.get');
Route::get('/detailed-tasks/{task}', [SearchTaskController::class, 'task'])->name("searchTask.task");
Route::post('/detailed-tasks', [SearchTaskController::class, 'compliance_save'])->name("searchTask.comlianse_save");
Route::get('/change-task/{task}', [SearchTaskController::class, 'changeTask'])->name("searchTask.changetask")->middleware('auth');
Route::put('/change-task/{task}', [UpdateController::class, 'change'])->name("update.__invoke")->middleware('auth');
Route::post('/change-task/{task}/delete-image', [UpdateController::class, 'deleteImage'])->name('task.deleteImage');
Route::get('admin/reported-tasks', [VoyagerTaskController::class, 'reported_tasks'])->name('admin.tasks.reported')->middleware('auth');
Route::get('admin/complete-task/{task}', [VoyagerTaskController::class, 'complete_task'])->name('admin.tasks.complete')->middleware('auth');
Route::delete('admin/complete-task/{task}', [VoyagerTaskController::class, 'delete_task'])->name('admin.tasks.reported.delete')->middleware('auth');
Route::get('task/{task}/map', [SearchTaskController::class, 'task_map'])->name('task.map');
#endregion

#region verificationInfo
Route::group(['middleware' => 'auth', 'prefix' => 'verification'], function () {
    Route::get('/', [ProfileController::class, 'verificationIndex'])->name('verification');

    Route::get('/verificationInfo', [ProfileController::class, 'verificationInfo'])->name('profile.verificationInfo');
    Route::post('/verificationInfoStore', [ProfileController::class, 'verificationInfoStore'])->name('profile.verificationInfoStore');

    Route::get('/verificationInfo/contact', [ProfileController::class, 'verificationContact'])->name('profile.verificationContact');
    Route::post('/verificationInfo/contact', [ProfileController::class, 'verificationContactStore'])->name('profile.verificationContactStore');

    Route::get('/verificationInfo/photo', [ProfileController::class, 'verificationPhoto'])->name('profile.verificationPhoto');
    Route::put('/verificationInfo/photo', [ProfileController::class, 'verificationPhotoStore'])->name('profile.verificationPhotoStore');

    Route::get('/verificationInfo/category', [ProfileController::class, 'verificationCategory'])->name('profile.verificationCategory');
    Route::post('/verificationInfo/category', [ProfileController::class, 'personalCategory'])->name('profile.personalCategory');
});
#endregion

#region footerpage
Route::get('/faq', [FaqsController::class, 'index'])->name('faq.index');
Route::get('/questions/{id}', [FaqsController::class, 'questions'])->name('faq.questions');
Route::get('/reviews',[Controller::class,'performer_reviews']);
Route::get('/author-reviews',[Controller::class,'authors_reviews']);
Route::get('/press', [Controller::class, 'index'])->name('massmedia');
Route::get('/geotaskshint', [Controller::class, 'geotaskshint'])->name('geotaskshint');
Route::get('/security', [Controller::class, 'security'])->name('security');
Route::get('/badges', [Controller::class, 'badges'])->name('badges');
Route::get('/news', [Controller::class, 'news'])->name('news');
Route::get('/news/{id}', [Controller::class, 'news_page'])->name('news.page');
Route::get('/privacy', [Controller::class, 'policy'])->name('privacy');
Route::get('/app',[Controller::class,'device']);
#endregion

#region Profile
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('profile')->group(function () {
        Route::post('/youtube_link', [ProfileController::class, 'youtube_link'])->name('youtube_link');
        Route::get('youtube_link_delete',[ProfileController::class,'youtube_link_delete'])->name('youtube_link_delete');
        Route::get('/', [ProfileController::class, 'profileData'])->name('profile.profileData');
        Route::get('/cash', [ProfileController::class, 'profileCash'])->name('profile.profileCash');
        Route::get('/settings', [ProfileController::class, 'editData'])->name('profile.editData');
        Route::post('/settings/update', [ProfileController::class, 'updateData'])->name('profile.updateData');
        Route::get('/clear-sessions', [ProfileController::class, 'clear_sessions'])->name('profile.clear_sessions');
        Route::post('/getcategory', [ProfileController::class, 'getCategory'])->name('profile.getCategory');
        Route::post('/store/profile/image', [ProfileController::class, 'storeProfileImage'])->name('profile.storeProfileImage');
        Route::post('/description', [ProfileController::class, 'editDescription'])->name('profile.EditDescription');
        Route::view('/create', 'profile/create_port');
        Route::post('/portfolio/create', [ProfileController::class, 'createPortfolio'])->name('profile.createPortfolio');
        Route::post('/portfolio/{portfolio}/delete-image', [ProfileController::class, 'deleteImage'])->name('profile.deleteImage');
        Route::post('/portfolio/{portfolio}/update', [ProfileController::class, 'updatePortfolio'])->name('profile.updatePortfolio');
        Route::get('/portfolio/{portfolio}', [ProfileController::class, 'portfolio'])->name('profile.portfolio');
        Route::post('/delete/portfolio/{portfolio}', [ProfileController::class, 'delete'])->name('profile.delete');
        Route::get('/notif_setting', [ProfileController::class, 'notif_setting_ajax'])->name('profile.notif_setting_ajax');
    });
});
Route::post('/set-session', [ProfileController::class, 'setSession'])->name('profile.set_session');
Route::post('/uploadImage', [ProfileController::class, 'uploadImage'])->name('profile.UploadImage');
#endregion

#region creat task
Route::prefix("task")->group(function () {
    Route::prefix("create")->group(function () {
        Route::get('/', [CreateController::class, 'name'])->name('task.create.name');
        Route::post('/name', [CreateController::class, 'name_store'])->name('task.create.name.store');
        Route::get('/remote/{task}', [CreateController::class, 'remote_get'])->name('task.create.remote');
        Route::post('/remote/{task}', [CreateController::class, 'remote_store'])->name('task.create.remote.store');
        Route::get('/custom/{task}', [CreateController::class, 'custom_get'])->name('task.create.custom.get');
        Route::post('/custom/{task}/store', [CreateController::class, 'custom_store'])->name('task.create.custom.store');
        Route::get('/address/{task}', [CreateController::class, 'address'])->name('task.create.address');
        Route::post('/address/{task}/store', [CreateController::class, 'address_store'])->name('task.create.address.store');
        Route::get('/date/{task}', [CreateController::class, 'date'])->name('task.create.date');
        Route::post('/date/{task}/store', [CreateController::class, 'date_store'])->name('task.create.date.store');
        Route::get('/budget/{task}', [CreateController::class, 'budget'])->name('task.create.budget');
        Route::post('/budget/{task}/store', [CreateController::class, 'budget_store'])->name('task.create.budget.store');
        Route::get('/note/{task}', [CreateController::class, 'note'])->name('task.create.note');
        Route::post('/note/{task}/store', [CreateController::class, 'note_store'])->name('task.create.note.store');
        Route::post('/note/{task}/images/store', [CreateController::class, 'images_store'])->name('task.create.images.store');
        Route::get('/contact/{task}', [CreateController::class, 'contact'])->name('task.create.contact');
        Route::post('/contact/{task}/store', [CreateController::class, 'contact_store'])->name('task.create.contact.store.phone')->middleware('auth');
        Route::post('/contact/{task}/store/register', [CreateController::class, 'contact_register'])->name('task.create.contact.store.register')->middleware('guest');
        Route::post('/contact/{task}/store/login/', [CreateController::class, 'contact_login'])->name('task.create.contact.store.login')->middleware('guest');
        Route::get('/verify/{task}/{user}', [CreateController::class, 'verify'])->name('task.create.verify');
        Route::post('/verify/{user}', [UserController::class, 'verifyProfile'])->name('task.create.verification');
        Route::post('/upload', [CreateController::class, 'uploadImage']);
        Route::get('task/{task}/images/delete', [CreateController::class, 'deleteAllImages'])->name('task.images.delete')->middleware('auth');
        Route::post("/detailed-task/{task}/response", [ResponseController::class, 'store'])->name('task.response.store');
    });
});
#endregion

#region
Route::post('select-performer/{response}', [ResponseController::class, 'selectPerformer'])->name('response.selectPerformer');
Route::post('tasks/{task}/not-complete', [UpdateController::class, 'not_completed'])->name('update.not_completed');
Route::post('send-review-user/{task}', [UpdateController::class, 'sendReview'])->name('update.sendReview');
Route::get('/categories/{id}', [Controller::class, 'category'])->name("categories");
Route::get('/lang/{lang}', [Controller::class, 'lang'])->name('lang');
Route::get('/', [Controller::class, 'home'])->name('home');
Route::get('/terms',[Controller::class,'terms'])->name('terms_mobile_url');
Route::get('/terms/{lang}',[Controller::class,'terms_mobile']);
Route::get('/paynet-oplata',[Controller::class,'paynet_oplata'])->name('paynet_oplata_url');
Route::get('/paynet-oplata/{lang}',[Controller::class,'paynet_mobile']);
Route::get('/show-notification/{notification}', [NotificationController::class, 'show_notification'])->name('show_notification');
Route::get('/show-notification-user/{notification}', [NotificationController::class, 'show_notification_user'])->name('show_notification_user');
Route::get('/read-notification/{notification}', [NotificationController::class, 'read_notification'])->name('read_notification');
Route::get('/read-all-notification/{user_id}', [NotificationController::class, 'read_all_notification'])->name('read_all_notification');
#endregion

#region registration
Route::get('login/facebook', [SocialController::class, 'facebookRedirect'])->name('social.facebookRedirect');
Route::get('login/facebook/callback', [SocialController::class, 'loginWithFacebook']);
Route::get('login/google', [SocialController::class, 'googleRedirect'])->name('social.googleRedirect');
Route::get('login/google/callback', [SocialController::class, 'loginWithGoogle']);
Route::get('login/apple', [SocialController::class, 'appleRedirect'])->name('social.appleRedirect');
Route::post('login/apple/callback', [SocialController::class, 'loginWithApple']);
Route::get('/login', [LoginController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.loginPost')->middleware('guest');
Route::get('/register', [UserController::class, 'signup'])->name('user.signup')->middleware('guest');
Route::post('/register', [LoginController::class, 'customRegister'])->name('login.customRegister')->middleware('guest');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');
Route::get('/reset', [UserController::class, 'reset'])->name('user.reset');
Route::get('/confirm', [UserController::class, 'confirm'])->name('user.confirm');
Route::get('account/verify/{user}/{hash}', [LoginController::class, 'verifyAccount'])->name('login.verifyAccount');
Route::get('account/verification/email', [LoginController::class, 'send_email_verification'])->name('login.send_email_verification')->middleware('auth');
Route::get('account/verification/phone', [LoginController::class, 'send_phone_verification'])->name('login.send_phone_verification')->middleware('auth');
Route::post('account/verification/phone', [LoginController::class, 'verify_phone'])->name('login.verify_phone')->middleware('auth');
Route::post("account/change/email", [LoginController::class, 'change_email'])->name('login.change_email')->middleware('auth');
Route::post("account/change/phone", [LoginController::class, 'change_phone_number'])->name('login.change_phone_number')->middleware('auth');
Route::post('/reset', [UserController::class, 'reset_submit'])->name('user.reset_submit');
Route::post('/reset-by-email', [UserController::class, 'reset_by_email'])->name('user.reset_submit_email');
Route::get('/reset/password', [UserController::class, 'reset_password'])->name('user.reset_password');
Route::post('/reset/password', [UserController::class, 'reset_password_save'])->name('user.reset_password_save');
Route::get('/code', [UserController::class, 'reset_code_view'])->name('user.reset_code_view');
Route::view('/code-email', 'auth.codeEmail')->name('user.reset_code_view_email');
Route::post('/code', [UserController::class, 'reset_code'])->name('user.reset_code');
Route::get('/register/code', [UserController::class, 'code'])->name('user.code');
Route::get('/self-delete', [UserController::class, 'self_delete'])->name('self.delete');
Route::post('/confirmation-self-delete', [UserController::class, 'confirmationSelfDelete'])->name('confirmation.self.delete');
Route::post('/account/password/change', [ProfileController::class, 'change_password'])->name('profile.change_password');
#endregion

Route::any('/paynet', function () {
    (new PayUz)->driver('paynet')->handle();
});
// Show transactions history
Route::get('profile/transactions/history', [UserTransactionHistory::class, 'getTransactions'])->name('user.transactions.history')->middleware('auth');

