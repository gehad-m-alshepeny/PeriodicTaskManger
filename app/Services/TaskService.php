<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class TaskService
{
    /**
     * @var $taskRepository
     */
    protected $taskRepository;

    /**
     * TaskService constructor.
     *
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }


    /**
     * Get all task.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->taskRepository->getAll();
    }
  

    /**
     * Validate task data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function saveTaskData($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required',
            'repeat_every' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->taskRepository->save($data);

        return $result;
    }

}