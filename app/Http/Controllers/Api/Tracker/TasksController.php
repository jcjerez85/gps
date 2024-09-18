<?php

namespace App\Http\Controllers\Api\Tracker;

use Validator;
use Tobuli\Entities\Task;
use Tobuli\Entities\TaskStatus;

use App\Exceptions\ResourseNotFoundException;
use App\Exceptions\PermissionException;
use Tobuli\Exceptions\ValidationException;

class TasksController extends ApiController
{
    public function getTasks() {
        return response()->json([
            'status' => 1,
            'data'    => Task::with('lastStatus')->where('device_id', $this->deviceInstance->id)->get()
        ]);
    }

    public function getStatuses() {
        return response()->json([
            'status' => 1,
            'data' => array_chunk(TaskStatus::$statuses, 1, true)
        ], 200, [], true);
    }

    public function getSignature($taskStatusId) {
        $taskStatus = TaskStatus::find($taskStatusId);

        if ( ! $taskStatus)
            throw new ResourseNotFoundException('global.task_status');

        if ( ! $taskStatus->signature)
            throw new ResourseNotFoundException('global.task_status');

        return response($taskStatus->signature)
            ->header('Content-Type', 'image/jpeg')
            ->header('Pragma', 'public')
            ->header('Content-Disposition', 'inline; filename="photo.jpeg"')
            ->header('Cache-Control', 'max-age=60, must-revalidate');
    }

    public function update($taskId) {
        $validator = Validator::make(request()->all(), [
            'status'    => 'required|in:' . implode(',', array_keys(TaskStatus::$statuses)),
            'signature' => 'required_if:status,' . TaskStatus::STATUS_COMPLETED,
        ]);

        if ( $validator->fails() )
            throw new ValidationException($validator->errors());

        $task = Task::find($taskId);

        if ( ! $task)
            throw new ResourseNotFoundException('global.task');

        if ($task->device_id != $this->deviceInstance->id)
            throw new PermissionException();

        $taskStatus = new TaskStatus();
        $taskStatus->task_id = $task->id;
        $taskStatus->status = request()->input('status');

        if ( ! empty(request()->input('signature'))) {
            $taskStatus->signatureBase64 = request()->input('signature');
        }

        $taskStatus->save();

        $task->status = (int) request()->input('status');
        $task->save();

        return response()->json(['status' => 1]);
    }
}