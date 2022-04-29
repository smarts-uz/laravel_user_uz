<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class CustomFieldAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/custom-field-by-category/{category}",
     *     tags={"Task"},
     *     summary="Get Custom fields By Category ID",
     *     security={
     *      {"token": {}},
     *     },
     *     @OA\Parameter(
     *          in="path",
     *          name="category",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *     ),
     *     @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function getByCategoryId(Category $category)
    {

        return $category->custom_fields;
    }


    
    public function getByCustomFieldId(CustomField $custom_field)
    {

        return $custom_field->custom_field_values;
    }

    /**
     * @OA\Get(
     *     path="/api/custom-field-values-by-task/{task}",
     *     tags={"Task"},
     *     summary="Get Custom field Values by Task ID",
     *     security={
     *      {"token": {}},
     *     },
     *     @OA\Parameter(
     *          in="path",
     *          name="task",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *     ),
     *     @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function getByTaskId(Task $task)
    {
        return $task->custom_field_values;
//        $data = Task::find($id);
//
//        if($data){
//            return response()->json($data, 200);
//        }
//        return response()->json('Бундай id ли task йок', 404);
    }
}
