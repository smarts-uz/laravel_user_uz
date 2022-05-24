<?php


use App\Http\Controllers\API\TaskAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FaqsController;

// javoxir
use App\Http\Controllers\LoginController;

//avacoder
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;

//++
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Task\ResponseController;
use App\Http\Controllers\Task\UpdateController;
use App\Http\Controllers\UserController;

//avocoder
use App\Http\Controllers\UserTransactionHisory;
use App\Http\Controllers\vendor\Chatify\MessagesController;
use App\Http\Controllers\VoyagerTaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;

// javoxir
use App\Http\Controllers\SocialController;

// avocoder
use App\Http\Controllers\PerformersController;

// javoxir
use App\Http\Controllers\RefillController;

// javoxir
use App\Http\Controllers\Task\SearchTaskController;

// javoxir
use App\Http\Controllers\admin\VoyagerUserController;

// javoxir
use App\Http\Controllers\Task\CreateController;

use App\Http\Controllers\ClickuzController;
use Teamprodev\LaravelPayment\PayUz;

//avocoder

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




#region performers
Route::get('/for_del_new_task/{task}', [CreateController::class, 'deletetask']); // javoxir
Route::group(['middleware' => 'auth'], function () {
    Route::delete('/fordelnotif/{notification}/', [PerformersController::class, 'deleteNotification'])->name('performers.deleteNotification'); // javoxir
});
Route::post('del-notif', [PerformersController::class, 'del_all_notif']); // javoxir

Route::get('perf-ajax/{id}', [PerformersController::class, 'perf_ajax']); // javoxir
Route::get('active-performers', [PerformersController::class, 'ajaxAP'])->name('performers.active_performers'); // Shuxrat78
Route::post('give-task', [PerformersController::class, 'give_task']); // javoxir

Route::group(['prefix' => 'performers'], function () {
    Route::post('/', [PerformersController::class, 'service']); // javoxir
    Route::get('/', [PerformersController::class, 'service'])->name('performers.service'); // javoxir
    Route::get('/{user}', [PerformersController::class, 'performer'])->name('performers.performer'); // javoxir
    Route::get('/chat/{id}', [PerformersController::class, 'performer_chat'])->name('performers.performer_chat'); // javoxir
});

#endregion
Route::get('task/{task}/map', [TaskAPIController::class, 'task_map'])->name('task.map'); //end

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

Route::get('/report/request/{id}',[ReportController::class,'report'])->name('report');
Route::post('/request',[ReportController::class,'request'])->name('request');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get("report", [ReportController::class, "index"])->name("index");
    Route::get("report/get", [ReportController::class, "report"])->name("report");
    Route::get("childreport/get", [ReportController::class, "childreport"])->name("child.report");
    Route::get("report/{id}", [ReportController::class, "show_child"])->name("show.child");
    Route::get("users/activitiy/{user}", [VoyagerUserController::class, "activity"])->name("voyagerUser.activity"); // javoxir
    Route::get('/messages/chat/{id}', [ConversationController::class, 'showChat'])->name("conversation.showChat"); // javoxir
    Route::post('/messages/chat/rate/{message}', [ConversationController::class, 'rating'])->name("conversation.rating"); // javoxir
    Route::post('/messages/chat/close/{message}', [ConversationController::class, 'close'])->name("conversation.close"); // javoxir
    Route::post('/messages/chat/{id}', [ConversationController::class, 'send'])->name("conversation.send"); // javoxir
});
#endregion

#region tasks
Route::group(['middleware' => 'auth'], function () {
    Route::get('/my-tasks', [Controller::class, 'my_tasks'])->name('searchTask.mytasks'); // javoxir
});
Route::get('task-search', [SearchTaskController::class, 'search_new'])->name('searchTask.task_search'); // javoxir
Route::post('tasks-search', [SearchTaskController::class, 'search_new2'])->name('searchTask.ajax_tasks');
Route::get('search', [SearchTaskController::class, 'search'])->name('search'); // javoxir
Route::post('ajax-request', [SearchTaskController::class, 'task_response']); // javoxir
Route::delete('delete-task/{task}', [SearchTaskController::class, 'delete_task'])->name('searchTask.delete_task'); // javoxir
Route::get('delete-task/{task}', [SearchTaskController::class, 'delete_task'])->name('searchTask.delete_task.get'); // javoxir
Route::get('/detailed-tasks/{task}', [SearchTaskController::class, 'task'])->name("searchTask.task"); // javoxir
Route::post('/detailed-tasks', [SearchTaskController::class, 'comlianse_save'])->name("searchTask.comlianse_save");
Route::get('/change-task/{task}', [SearchTaskController::class, 'changeTask'])->name("searchTask.changetask")->middleware('auth'); // javoxir
Route::put('/change-task/{task}', [UpdateController::class, 'change'])->name("update.__invoke")->middleware('auth'); // javoxir
Route::get('admin/reported-tasks', [VoyagerTaskController::class, 'reported_tasks'])->name('admin.tasks.reported')->middleware('auth');
Route::get('admin/complete-task/{task}', [VoyagerTaskController::class, 'complete_task'])->name('admin.tasks.complete')->middleware('auth');
Route::delete('admin/complete-task/{task}', [VoyagerTaskController::class, 'delete_task'])->name('admin.tasks.reported.delete')->middleware('auth');
#endregion

#region verificationInfo
Route::view('/offer-tasks', 'task.offertasks');
Route::group(['middleware' => 'auth', 'prefix' => 'verification'], function () {
    Route::get('/', [ProfileController::class, 'verificationIndex'])->name('verification'); // javoxir

    Route::get('/verificationInfo', [ProfileController::class, 'verificationInfo'])->name('profile.verificationInfo'); // javoxir
    Route::post('/verificationInfoStore', [ProfileController::class, 'verificationInfoStore'])->name('profile.verificationInfoStore'); // javoxir

    Route::get('/verificationInfo/contact', [ProfileController::class, 'verificationContact'])->name('profile.verificationContact'); // javoxir

    Route::post('/verificationInfo/contact', [ProfileController::class, 'verificationContactStore'])->name('profile.verificationContactStore'); // javoxir

    Route::get('/verificationInfo/photo', [ProfileController::class, 'verificationPhoto'])->name('profile.verificationPhoto'); // javoxir
    Route::put('/verificationInfo/photo', [ProfileController::class, 'verificationPhotoStore'])->name('profile.verificationPhotoStore'); // javoxir

    Route::get('/verificationInfo/category', [ProfileController::class, 'verificationCategory'])->name('profile.verificationCategory'); // javoxir
    Route::post('/verificationInfo/category', [ProfileController::class, 'getCategory'])->name('profile.getCategory'); // javoxir
});
#endregion

#region foterpage
Route::get('/faq', [FaqsController::class, 'index'])->name('faq.index'); // javoxir
Route::get('/questions/{id}', [FaqsController::class, 'questions'])->name('faq.questions'); // javoxir
Route::view('/faq', 'faq.faq');
Route::view('/reviews', 'reviews.review');
Route::view('/author-reviews', 'reviews.authors_reviews');
Route::get('/press', [Controller::class, 'index'])->name('massmedia'); // javoxir
Route::view('/vacancies', 'reviews.vacancies');
Route::get('/geotaskshint', [Controller::class, 'geotaskshint'])->name('geotaskshint'); // javoxir
Route::get('/security', [Controller::class, 'security'])->name('security'); // javoxir
Route::get('/badges', [Controller::class, 'badges'])->name('badges'); // javoxir
Route::get('/news', [Controller::class, 'news'])->name('news'); // javoxir
#endregion

#region Profile
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('profile')->group(function () {
        Route::post('/youtube_link', [ProfileController::class, 'youtube_link'])->name('youtube_link');
        Route::get('youtube_link_delete',[ProfileController::class,'youtube_link_delete'])->name('youtube_link_delete');
        //Profile
        Route::get('/', [ProfileController::class, 'profileData'])->name('profile.profileData'); // javoxir
        //Profile cash
        Route::get('/cash', [ProfileController::class, 'profileCash'])->name('profile.profileCash'); // javoxir

        // Profile settings
        Route::get('/settings', [ProfileController::class, 'editData'])->name('profile.editData');
        Route::post('/settings/update', [ProfileController::class, 'updateData'])->name('profile.updateData');
        Route::get('/clear-sessions', [ProfileController::class, 'clear_sessions'])->name('profile.clear_sessions');

        // Profile delete
        Route::get('/delete/{id}', [ProfileController::class, 'destroy'])->name('profile.destroy'); // javoxir


        //added category id
        Route::post('/getcategory', [ProfileController::class, 'getCategory'])->name('profile.getCategory'); // javoxir

        Route::post('/storeDistrict', [ProfileController::class, 'StoreDistrict'])->name('profile.StoreDistrict'); // javoxir

        Route::post('/store/profile/image', [ProfileController::class, 'storeProfileImage'])->name('profile.storeProfileImage'); // javoxir
        Route::post('/comment', [ProfileController::class, 'comment'])->name('profile.comment'); // javoxir
        Route::post('/testBase', [ProfileController::class, 'testBase'])->name('profile.testBase'); // javoxir

        //description
        Route::post('/description', [ProfileController::class, 'EditDescription'])->name('profile.EditDescription'); // javoxir

        //create_port
        Route::view('/create', 'profile/create_port');
        Route::post('/portfolio/create', [ProfileController::class, 'createPortfolio'])->name('profile.createPortfolio'); // javoxir
        Route::get('/portfolio/{portfolio}', [ProfileController::class, 'portfolio'])->name('profile.portfolio'); // javoxir
        Route::post('/delete/portfolio/{portfolio}', [ProfileController::class, 'delete'])->name('profile.delete'); // javoxir
        Route::get('/notif_setting', [ProfileController::class, 'notif_setting_ajax'])->name('profile.notif_setting_ajax');
    });
});
Route::post('/set-session', [ProfileController::class, 'setSession'])->name('profile.set_session');
Route::post('/uploadImage', [ProfileController::class, 'UploadImage'])->name('profile.UploadImage');
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
        Route::get('/verify/{task}/{user}/{data}', [CreateController::class, 'verify2'])->name('task.create.verify.phone');
        Route::post('/verify/{user}', [UserController::class, 'verifyProfil'])->name('task.create.verification');
        Route::post('/verify/{task}', [UserController::class, 'verifyProfil2'])->name('task.create.verification.phone');
        Route::post('/upload', [CreateController::class, 'uploadImage']);
        Route::get('task/{task}/images/delete', [CreateController::class, 'deleteAllImages'])->name('task.images.delete')->middleware('auth');
        Route::post("/detailed-task/{task}/response", [ResponseController::class, 'store'])->name('task.response.store'); // javoxir
    });
});
#endregion

#region
Route::post('select-performer/{response}', [ResponseController::class, 'selectPerformer'])->name('response.selectPerformer'); // javoxir
Route::post('tasks/{task}/complete', [UpdateController::class, 'completed'])->name('update.completed'); // javoxir
Route::post('tasks/{task}/not-complete', [UpdateController::class, 'not_completed'])->name('update.not_completed'); // javoxir
Route::post('send-review-user/{task}', [UpdateController::class, 'sendReview'])->name('update.sendReview'); // javoxir
Route::get('/categories/{id}', [Controller::class, 'category'])->name("categories"); // javoxir
Route::get('/lang/{lang}', [Controller::class, 'lang'])->name('lang'); // javoxir
Route::get('/', [Controller::class, 'home'])->name('home'); // javoxir
Route::get('/terms', function () {
    return view('auth.terms');
});
Route::get('/file-download', [Controller::class, 'download'])->name('file_download.download');
Route::get('/show-notification/{notification}', [NotificationController::class, 'show_notification'])->name('show_notification');
Route::get('/read-notification/{notification}', [NotificationController::class, 'read_notification'])->name('read_notification');
#endregion

#region registration
Route::get('login/facebook', [SocialController::class, 'facebookRedirect'])->name('social.facebookRedirect');
Route::get('login/facebook/callback', [SocialController::class, 'loginWithFacebook']);
Route::get('login/google', [SocialController::class, 'googleRedirect'])->name('social.googleRedirect');
Route::get('login/google/callback', [SocialController::class, 'loginWithGoogle']);
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
Route::post("account/change/phone/send", [LoginController::class, 'verify_phone'])->name('login.verify_phone')->middleware('auth');
Route::post('/reset', [UserController::class, 'reset_submit'])->name('user.reset_submit');
Route::post('/reset-by-email', [UserController::class, 'reset_by_email'])->name('user.reset_submit_email');
Route::get('/reset/password', [UserController::class, 'reset_password'])->name('user.reset_password');
Route::post('/reset/password', [UserController::class, 'reset_password_save'])->name('user.reset_password_save');
Route::get('/code', [UserController::class, 'reset_code_view'])->name('user.reset_code_view');
Route::view('/code-email', 'auth.codeEmail')->name('user.reset_code_view_email');
Route::post('/code', [UserController::class, 'reset_code'])->name('user.reset_code');
Route::get('/register/code', [UserController::class, 'code'])->name('user.code');
Route::post('/register/code', [UserController::class, 'code_submit'])->name('user.code_submit');
Route::post('/account/password/change', [ProfileController::class, 'change_password'])->name('profile.change_password');
#endregion

#region payments
Route::post('/get_info_click', [ClickuzController::class, 'get_info']);
Route::get('/test_gic', [ClickuzController::class, 'test']);
Route::get('send', [RefillController::class, 'ref'])->name('paycom.send'); // javoxir
Route::get('/ref', 'App\Http\Controllers\RefillController@ref'); // javoxir
Route::post('/prepare', "App\Http\Controllers\RefillController@prepare")->name('prepare'); // javoxir
Route::post('/complete', "App\Http\Controllers\RefillController@complete")->name('complete'); // javoxir
Route::post('/paycom', 'App\Http\Controllers\PaycomTransactionController@paycom')->name('paycom'); // javoxir
// Show transactions history
Route::get('profile/transactions/history', [UserTransactionHisory::class, 'getTransactions'])->name('user.transactions.history')->middleware('auth');

#region payments
Route::any('/paynet', function () {
    (new PayUz)->driver('paynet')->handle();
});
Route::any('/payme', function () {
    (new PayUz)->driver('payme')->handle();
});
Route::any('/click', function () {
    (new PayUz)->driver('click')->handle();
});
Route::any('/click/user-balance', function () {
    return (new PayUz())->click_additional();
});
#endregion



// payments

