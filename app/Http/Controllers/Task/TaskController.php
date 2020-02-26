<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends ApiController
{
    public function index()
    {
        $tasks = Task::all();
        return $this->showAll($tasks);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return $this->showOne($task);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'priority' => 'in:1,2,3'
        ];

        $this->validate($request, $rules);

        $fields = $request->all();

        $task = Task::create($fields);

        return $this->showOne($task, 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $rules = [
            'title' => 'required',
            'priority' => 'in:1,2,3'
        ];

        $this->validate($request, $rules);

        $fields = $request->all();

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

    public function destroy($id){
        $task = Task::findOrFail($id);
        $task->delete();

        return $this->showOne($task);
    }
}
