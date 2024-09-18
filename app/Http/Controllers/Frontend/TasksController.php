<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use CustomFacades\Repositories\TasksRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\TasksFormValidator;
use Tobuli\Entities\Device;
use Tobuli\Entities\Task;
use Tobuli\Entities\TaskStatus;

use App\Exceptions\ResourseNotFoundException;
use App\Exceptions\PermissionException;
use Tobuli\Exceptions\ValidationException;

class TasksController extends Controller {

    public function index()
    {
        $this->checkException('tasks', 'view');

        $statuses = array_replace(
            ['0' => '-- '.trans('admin.select').' --'],
            TaskStatus::getList());

        $data = [
            'tasks'      => TasksRepo::searchAndPaginate([
                'filter' => ['accessible_user_id' => $this->user->id],
                'sorting' => request('sorting', [])
            ], 'id', 'desc', 10),
            'devices'    => UserRepo::getDevices($this->user->id)
                ->pluck('name', 'id')
                ->prepend('-- '.trans('admin.select').' --', '0')
                ->all(),
            'statuses'   => $statuses,
            'priorities' => array_map(function($value) { return trans($value);}, Task::$priorities),
        ];

        return view('front::Tasks.index')->with($data);
    }

    public function search()
    {
        $this->checkException('tasks', 'view');

        $filter = ['accessible_user_id' => $this->user->id];

        if ( ! empty($this->data['search_device_id']))
            $filter['device_id'] = (int) $this->data['search_device_id'];

        if ( ! empty($this->data['search_task_status']))
            $filter['status'] = (int) $this->data['search_task_status'];

        if ( ! empty($this->data['search_time_from']))
            $filter['delivery_time_from'] = $this->data['search_time_from'];

        if ( ! empty($this->data['search_time_to']))
            $filter['delivery_time_to'] = $this->data['search_time_to'];

        if ( ! empty($this->data['search_invoice_number']))
            $filter['invoice_number'] = $this->data['search_invoice_number'];

        $tasks = TasksRepo::searchAndPaginate(['filter' => $filter, 'sorting' => request('sorting', [])], 'id', 'desc', 10);

        if ($this->api)
            return response()->json([
                'status' => 1,
                'items'  => collect(['url' => route('api.get_tasks')])->merge($tasks)
            ]);

        $data = [
            'tasks'    => $tasks,
            'devices'  => UserRepo::getDevices($this->user->id)->pluck('name', 'id')->all(),
            'statuses' => TaskStatus::getList(),
        ];

        return view('front::Tasks.list')->with($data);
    }

    public function store()
    {
        $this->checkException('tasks', 'store');

        TasksFormValidator::validate('create', $this->data);

        $device = Device::find($this->data['device_id']);

        $this->checkException('devices', 'own', $device);

        $task = new Task(request()->except('id'));
        $task->user_id = $this->user->id;
        $task->save();

        return response()->json([
            'status' => 1,
            'item'   => $task
        ]);
    }

    public function doDestroy($id = null) {

        $ids = request()->get('id', $id);

        if ( ! is_array($ids))
            $ids = [$ids];

        return view('front::Tasks.destroy')->with(['ids' => $ids]);
    }

    public function destroy() {
        $id = array_key_exists('task_id', $this->data) ? $this->data['task_id'] : $this->data['id'];

        if ( ! is_array($id))
            $ids = [$id];
        else
            $ids = $id;

        $tasks = Task::whereIn('id', $ids)->get();

        foreach ($tasks as $task)
        {
            if ( ! $this->user->can('remove', $task))
                continue;

            $task->delete();
        }

        return ['status' => 1];
    }

    public function show($id) {
        $item = TasksRepo::findWithAttributes($id);

        $this->checkException('tasks', 'show', $item);

        if ($this->api)
            return response()->json([
                'status' => 1,
                'item'   => $item
            ]);

        return view('front::Tasks.show')->with(['item' => $item]);
    }

    public function edit($id = null)
    {
        $item = TasksRepo::find($id);

        $this->checkException('tasks', 'edit', $item);

        $data = [
            'item'       => $item,
            'devices'    => UserRepo::getDevices($this->user->id)->pluck('name', 'id')->all(),
            'statuses'   => TaskStatus::getList(),
            'priorities' => array_map(function($value) { return trans($value);}, Task::$priorities),
        ];

        return view('front::Tasks.edit')->with($data);
    }

    public function update($id = null) {
        $task = TasksRepo::findWithAttributes($id ?? $this->data['id']);

        $this->checkException('tasks', 'update', $task);

        TasksFormValidator::validate('update', $this->data, $id ?? $this->data['id']);

        $device = Device::find($this->data['device_id']);

        $this->checkException('devices', 'own', $device);

        $task->fill($this->data);
        $task->save();

        return response()->json([
            'status' => 1
        ]);
    }

    public function getSignature($taskStatusId)
    {
        $taskStatus = TaskStatus::find($taskStatusId);

        if ( ! $taskStatus)
            throw new ResourseNotFoundException('global.task_status');

        $this->checkException('tasks', 'show', $taskStatus->task);

        if ( ! $taskStatus->signature)
            throw new ResourseNotFoundException('signature');

        return response($taskStatus->signature)
            ->header('Content-Type', 'image/jpeg')
            ->header('Pragma', 'public')
            ->header('Content-Disposition', 'inline; filename="photo.jpeg"')
            ->header('Cache-Control', 'max-age=60, must-revalidate');
    }

    public function getStatuses() {
        return response()->json([
            'status' => 1,
            'items'  => toOptions( TaskStatus::getList() )
        ]);
    }

    public function getPriorities() {
        return response()->json([
            'status' => 1,
            'items'  => toOptions( array_map(function($value) { return trans($value);}, Task::$priorities) )
        ]);
    }

    public function import()
    {
        return view('front::Tasks.import');
    }

    public function importSet()
    {
        $file = request()->file('file');

        if (is_null($file)) {
            throw new ResourseNotFoundException(trans('validation.attributes.file'));
        }

        if ( ! $file->isValid()) {
            return;
        }

        $manager = new \Tobuli\Importers\Task\TaskImportManager();
        $manager->setFieldsReadMap(request()->get('fields', []))
            ->import($file->getPathName());

        return response()->json(['status' => 1]);
    }

    public function assignForm()
    {
        $ids = request()->get('id', []);

        return view('front::Tasks.assign')->with([
            'tasks'      => TasksRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10),
            'devices'    => UserRepo::getDevices($this->user->id)->pluck('name', 'id')->all(),
            'ids'        => is_array($ids) ? $ids : [$ids],
        ]);

    }

    public function assign()
    {
        TasksFormValidator::validate('assign', $this->data);

        $device = Device::find($this->data['device_id']);

        $this->checkException('devices', 'own', $device);


        $tasks = Task::whereIn('id', $this->data['tasks'])->get();

        foreach ($tasks as $task)
        {
            if ( ! $this->user->can('edit', $task))
                continue;

            $task->device()->associate($device);
            $task->save();
        }

        return response()->json([
            'status' => 1
        ]);
    }
}
