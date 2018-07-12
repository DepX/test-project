<?php

use Acme\Model\Task as TaskModel;

class UsersTask extends \Simple\Cli\Task
{
    /**
     * php app\cli.php users
     *
     * or (default index index)
     *
     * php app\cli.php users index
     *
     * or witch parameters
     *
     * php app\cli.php users index 10
     */
    public function indexAction($limit = 10)
    {
        $tasksModel = TaskModel::find([
            'limit' => $limit
        ])->get();

        $lastKey = count($tasksModel) - 1;

        foreach ($tasksModel as $key => $item) {
            echo 'Task id: ' . $item->id . PHP_EOL;
            echo 'Created At: ' . $item->getCreatedAt()->format('d.m.Y \i\n H:i') . PHP_EOL;
            if ($lastKey != $key)
                echo '------------------' . PHP_EOL;
        }
    }
}