<?php

use App\Http\Controllers\API\CategoriesAPIController;
use App\Http\Controllers\API\CustomFieldAPIController;
use App\Http\Controllers\API\FaqAPIController;
use App\Http\Controllers\API\NewsAPIController;
use App\Http\Controllers\API\PerformerAPIController;
use App\Http\Controllers\API\ProfileAPIController;
use App\Http\Controllers\API\TaskAPIController;
use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\API\SearchAPIController;
use Illuminate\Http\Request;
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

Route::any('/{paysys}',function($paysys){
    (new Goodoneuz\PayUz\PayUz)->driver($paysys)->handle();
});
Route::any('/pay/{paysys}/{key}/{amount}',function($paysys, $key, $amount){
    $model = Goodoneuz\PayUz\Services\PaymentService::convertKeyToModel($key);
    $url = request('redirect_url','/'); // redirect url after payment completed
    $pay_uz = new Goodoneuz\PayUz\PayUz;
    $pay_uz
        ->driver($paysys)
        ->redirect($model, $amount, 860, $url);
});

Route::middleware('custom.auth:api')->group(function () {
    Route::post('logout', [UserAPIController::class, 'logout']);

    Route::post('task/create', [TaskAPIController::class, 'create']); //end
    Route::get('settings', [ProfileAPIController::class, 'settings']); //end


    Route::get('/my-tasks', [TaskAPIController::class, 'my_tasks']); //end
    Route::put('/change-task/{task}', [TaskAPIController::class, 'changeTask']);
    Route::get('/custom-field-by-category/{category}',[CustomFieldAPIController::class,'getByCategoryId']); //end
    Route::get('/custom-field-values-by-task/{task}',[CustomFieldAPIController::class,'getByTaskId']); //end
    Route::get('/custom-field-values-by-custom-field/{custom_field}',[CustomFieldAPIController::class,'getByCustomFieldId']); //end

});


//User Routes
Route::post('login', [UserAPIController::class, 'login']); //end
Route::post('register', [UserAPIController::class, 'register']); //end
Route::put('update/{id}', [UserAPIController::class, 'update']);
Route::delete('delete/{id}', [UserAPIController::class, 'destroy']);

// FAQ
Route::get('faq', [FaqAPIController::class, 'index']); //end
Route::get('faq/{id}', [FaqAPIController::class, 'questions']); //end



//Tasks
Route::get('task/{task}', [TaskAPIController::class, 'task']); //end
Route::get('find', [TaskAPIController::class, 'search']); //end
Route::get('tasks-search', [SearchAPIController::class, 'ajax_tasks'])->name('tasks.search'); //end


//Categories
Route::get('/categories', [CategoriesAPIController::class, 'index']); //end
Route::get('/categories/{id}', [CategoriesAPIController::class, 'show']); //end
//Performers
Route::get('/performers', [PerformerAPIController::class, 'service']); //end
Route::get('/performers/{performer}', [PerformerAPIController::class, 'performer']); //end

