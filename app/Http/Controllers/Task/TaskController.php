<?php

namespace App\Http\Controllers;

use App\Task;
use App\TaskCategory;
use Illuminate\Http\Request;

class TaskController extends ApiController
{
    public function index(Request $request, $taskCategoryId = null)
    {
        $orderBy = 'id';
        $orderByOrientation = 'ASC';

        if(!empty($request->order_by)){
            $orderBy = $request->order_by;
        }

        if(!empty($request->order_by_orientation)){
            $orderByOrientation = $request->order_by_orientation;
        }

        $tasks = Task::
            orderBy($orderBy, $orderByOrientation)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC');

        if(!empty($taskCategoryId)){
            $tasks = $tasks->where('task_category_id', '=', $taskCategoryId);
        }

        $tasks = $tasks->get();

        return $this->showAll($tasks);
    }

    public function show(Task $task)
    {
        return $this->showOne($task);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'priority' => 'in:1,2,3'
        ];

        $this->validate($request, $rules);

        $taskCategory = TaskCategory::find($request->get('task_category_id'));

        $task = new Task();
        $task->title = $request->get('title');

        if (!empty($request->get('priority'))) {
            $task->priority = $request->get('priority');
        }

        if (!empty($request->get('date'))) {
            $task->date = $request->get('date');
        }

        $task = $taskCategory->tasks()->save($task);

        return $this->showOne($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $rules = [
            'title' => 'required',
            'priority' => 'in:1,2,3'
        ];

        $this->validate($request, $rules);

        $fields = $request->all();

        if ($request->has('task_category_id')) {
            $task->task_category_id = $request->task_category_id;
        }

        if ($request->has('title')) {
            $task->title = $request->title;
        }

        if ($request->has('date')) {
            $task->date = $request->date;
        }

        if ($request->has('priority')) {
            $task->priority = $request->priority;
        }

        if ($request->has('completed')) {
            $task->completed = $request->completed;
        }

        if (!$task->isDirty()) {
            return $this->errorResponse('At least one value must be specified to update this record', 422);
        } else {
            $task->save();
            return $this->showOne($task);
        }
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return $this->showOne($task);
    }

    public function taskCategory(Task $task)
    {
        return $task->taskCategory;
    }
}
