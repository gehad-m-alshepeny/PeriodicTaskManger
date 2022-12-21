<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskRepetitions;
use App\Repositories\TaskRepositoryInterface;
use Carbon\Carbon;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * TaskRepository constructor.
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
     /**
     * Save task
     *
     * @param $data
     * @return Task
     */
    public function save($data)
    {
        $taskData = ['name' =>$data['name'] ,
                     'repeat_every'=>$data['repeat_every'],
                     'start_date'=>$data['repeat_every']=='Once' ? $data['repeat_at'][0] : $data['start_date'] ,
                     'end_date'=>$data['repeat_every']=='Once' ? $data['repeat_at'][0] : $data['end_date'] ,
                   ];
        $task = $this->task->create($taskData);
      //in cases every Monday, Wednesday and Friday OR every 5th of each month OR every 5th of March of each year
     if (!empty($data['repeat_at']) && is_array($data['repeat_at'])) 
        {
            $repeats = [];
            foreach (array_unique($data['repeat_at']) as $repeat_at) $repeats[] = ['repeat_at' => $repeat_at];
            if (!empty($repeats)) $task->taskRepetitions()->createMany($repeats);
        }
        $task->taskInstances()->create(['status' =>'Pending']);
    
        return $task->fresh();
    }

    /**
     * Get all tasks.
     *
     * @return Task $task
     */
    public function getAll()
    {
        $statDate= Carbon::now()->format('Y-m-d');
        $endDate=$this->formatEndDate(request()->groupBy);

        $tasks = $this->task->select('tasks.id','tasks.name','tasks.repeat_every','task_repetitions.repeat_at')
                            ->leftJoin('task_repetitions', 'tasks.id', '=', 'task_repetitions.task_id')
                            ->whereNot('tasks.start_date','>', $endDate) 
                            ->whereNot('tasks.end_date','<', $statDate)->get();

        $result=$this->formatTaskList($tasks,request()->groupBy);
        return $result;
    }

     /**
     * Get formatTaskList.
     *
     * @return Task $task
     */
    private function formatTaskList($tasks,$groupBy)
    {
     $outputList=[];
     $endDate= Carbon::parse($this->formatEndDate($groupBy)->format('Y-m-d'));
   
     $statDate = Carbon::now();
     $stratDateMonth=$statDate->month;
     $stratDateYear=$statDate->year;

    foreach($tasks as $task)
        {
            $loopIndicatorDate=Carbon::now();
            $taskObject=['task_id' =>$task->id ,'task_name'=>$task->name, 'task_date'=>$loopIndicatorDate->format('Y-m-d')];

            if($task->repeat_every=='Daily' || $task->repeat_every=='Weekly')
            { 
                while( $endDate->gte($loopIndicatorDate->format('Y-m-d'))) 
                {   
                    if($task->repeat_every=='Daily' || 
                    ($task->repeat_every=='Weekly' && $this->formatWeekDay($loopIndicatorDate->format('l')) == (int)$task->repeat_at))
                     $outputList[] = $taskObject;
                    $loopIndicatorDate->addDay()->format('Y-m-d');
                }
            }
            elseif($task->repeat_every=='Monthly')
            {
                $formatedDate=$stratDateYear.'-'.$stratDateMonth.'-'.$task->repeat_at;
                $loopIndicatorDate= Carbon::parse($formatedDate);
                while( $endDate->gte($loopIndicatorDate)) 
                {
                    if($loopIndicatorDate->gte(Carbon::now())) 
                    $outputList[] = $taskObject;       
                    $loopIndicatorDate->addMonth()->format('Y-m-d');
                }
            }elseif($task->repeat_every=='Yearly') {

                $explodDate=explode("-",$task->repeat_at);
                $formatedDate=Carbon::parse($stratDateYear.'-'.$explodDate[0].'-'.$explodDate[1]);
                $taskObject=['task_id' =>$task->id , 'task_name'=>$task->name,'task_date'=>$formatedDate->format('Y-m-d')];

                if($formatedDate->gte(Carbon::now()) && $endDate->gte($formatedDate))
                  $outputList[] = $taskObject;   
                    else{
                        $formatedDate->addYear()->format('Y-m-d');
                        $outputList[] = $taskObject;   ;   
                    }

            }else{ //once case
            $outputList[] = ['task_id' =>$task->id ,'task_name'=>$task->name, 'task_date'=>$task->repeat_at,];
            }
        }
 
        return $outputList;
    }

    private function formatEndDate($groupBy)
    {
        $currentDate=Carbon::now();
        $endDate = match ($groupBy) {
            'Today' =>  $currentDate,
            'Tommorrow' => $currentDate->addDays(1)->format('Y-m-d'),
            'Next_Week' => $currentDate->addDays(7)->format('Y-m-d'),
            'Next_Month'=> $currentDate->addMonth()->format('Y-m-d'),
            'Next_Year'=> $currentDate->addYear()->format('Y-m-d'),
            default => 'Invalid Input !',
        };
        return $endDate;
    }

    private function formatWeekDay($day)
         {
        $weekDay = match ($day) {
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday'=> 3,
            'Thursday' =>4 ,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' =>7 ,
            default => 'Invalid Input !',
        };
        return $weekDay;
       }
     /**
     * Get task by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
      //
    }

    /**
     * Update task
     *
     * @param $data
     * @return task
     */
    public function update($data, $id)
    { 
      //
    }

    /**
     * Update task
     *
     * @param $data
     * @return task
     */
    public function delete($id)
    {
      //
    }

}