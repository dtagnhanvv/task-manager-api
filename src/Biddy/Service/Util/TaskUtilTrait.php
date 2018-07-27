<?php


namespace Biddy\Service\Util;

use Biddy\Model\Core\TaskInterface;

trait TaskUtilTrait
{
    /**
     * @param $tasks
     * @return array
     */
    public function serializeTasks($tasks)
    {
        $groups = [];
        foreach ($tasks as $task) {
            if (!$task instanceof TaskInterface) {
                continue;
            }

            $groups[] = $this->serializeSingleTask($task);
        }

        return $groups;
    }

    /**
     * @param TaskInterface $task
     * @return array
     */
    public function serializeSingleTask(TaskInterface $task)
    {
        $group = [];
        $group['id'] = $task->getId();
        $group['createdDate'] = $task->getCreatedDate();

        $group['project'] = $task->getProject();
        $group['releasePlan'] = $task->getReleasePlan();
        $group['board'] = $task->getBoard();
        $group['cardNumber'] = $task->getCardNumber();
        $group['url'] = $task->getUrl();
        $group['status'] = $task->getStatus();
        $group['review'] = $task->getReview();

        return $group;
    }
}