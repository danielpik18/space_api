<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaskCategory;

class TaskCategoryController extends ApiController
{
    public function index(){
        $task_categories = TaskCategory::
            orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();
        return $this->showAll($task_categories);
    }

    public function show(TaskCategory $taskCategory)
    {
        return $this->showOne($taskCategory);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $fields = $request->all();

        $taskCategory = TaskCategory::create($fields);

        return $this->showOne($taskCategory, 201);
    }

    public function update(Request $request, TaskCategory $taskCategory)
    {
        $rules = [
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $fields = $request->all();

        if ($request->has('name')) {
            $taskCategory->name = $request->name;
        }

        if ($request->has('color')) {
            $taskCategory->color = $request->color;
        }

        if ($request->has('icon_class')) {
            $taskCategory->icon_class = $request->icon_class;
        }

        if (!$taskCategory->isDirty()) {
            return $this->errorResponse('At least one value must be specified to update this record', 422);
        } else {
            $taskCategory->save();
            return $this->showOne($taskCategory);
        }
    }

    public function destroy(TaskCategory $taskCategory){
        $taskCategory->delete();

        return $this->showOne($taskCategory);
    }

    public function tasks(TaskCategory $taskCategory){
        return $taskCategory->tasks;
    }
}
