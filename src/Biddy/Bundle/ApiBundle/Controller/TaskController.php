<?php

namespace Biddy\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Biddy\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Biddy\Handler\HandlerInterface;
use Biddy\Model\Core\TaskInterface;

/**
 * @Rest\RouteResource("Task")
 */
class TaskController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * Get all tasks
     *
     * @Rest\View(serializerGroups={"task.summary", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\TaskInterface[]
     */
    public function cgetAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');

        $taskRepository = $this->get('biddy.repository.task');
        $qb = $taskRepository->getTasksForUserQuery($user, $this->getParams());

        $pagination = $this->getPagination($qb, $request);
        $pagination['records'] = $this->serializeTasks($pagination['records'], $this->get('biddy.domain_manager.auction'));
        $pagination['totalUnread'] = $taskRepository->getTotalUnread($user);
        
        return $pagination;
    }

    /**
     * Get unread tasks count
     *
     * @Rest\View(serializerGroups={"task.detail", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\TaskInterface[]
     */
    public function cgetUnreadAction(Request $request)
    {
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $taskRepository = $this->get('biddy.repository.task');

        return $taskRepository->getTotalUnread($user);
    }


    /**
     * Get all tasks
     * @Rest\Get("/tasks/list")
     * @Rest\View(serializerGroups={"task.detail", "user.summary"})
     *
     * @Rest\QueryParam(name="account", nullable=true, requirements="\d+", description="the account id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="types", nullable=true, description="the type to get")
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Biddy\Model\Core\TaskInterface[]
     */
    public function cgetTasksAction(Request $request)
    {
        $taskRepository = $this->get('biddy.repository.task');
        $user = $this->getUserDueToQueryParamAccount($request, 'account');
        $params = array_merge($request->request->all(), $request->query->all());
        $qb = $taskRepository->getTasksForUserQuery($user, $this->getParams());

        if (!isset($params['page'])) {
            return $qb->getQuery()->getResult();
        } else {
            return $this->getPagination($qb, $request);
        }
    }

    /**
     * Get a single task group for the given id
     *
     * @Rest\View(serializerGroups={"task.detail", "user.summary"})
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return TaskInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a task from the submitted data
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postAction(Request $request)
    {
        return $this->post($request);
    }

    /**
     * Update an existing task from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when the resource is created",
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function putAction(Request $request, $id)
    {
        return $this->put($request, $id);
    }

    /**
     * Update an existing task from the submitted data or create a new task at a specific location
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request, $id)
    {
        return $this->patch($request, $id);
    }

    /**
     * Delete an existing task
     *
     * @ApiDoc(
     *  section = "Task",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteAction($id)
    {
        return $this->delete($id);
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'task';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_task';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.task');
    }
}