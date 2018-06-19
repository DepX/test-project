<?php

namespace Acme\Controller;

use Acme\Model\Task as TaskModel;

class IndexController extends ControllerBase
{
    /**
     * Front page
     */
    public function indexAction()
    {
        $params = [];
        $returnOrder = '';

        $order = $this->request->get('order');

        // check request
        if ($order) {
            $orderArray = explode(':', $order);
            if (count($orderArray) == 2) {
                if (in_array($orderArray[1], ['asc', 'desc'])) {
                    $orderBy = ($orderArray[1] == 'desc') ? ' DESC' : '';
                    // set parameters model
                    switch ($orderArray[0]) {
                        case "name":
                            $params = ['order' => 'user_name' . $orderBy];
                            $returnOrder = $order;
                            break;
                        case "email":
                            $params = ['order' => 'email' . $orderBy];
                            $returnOrder = $order;
                            break;
                        case "status":
                            $params = ['order' => 'status' . $orderBy];
                            $returnOrder = $order;
                            break;
                    }
                }
            }
        }

        $taskModel = TaskModel::find($params);

        $this->render('index/index', [
            'title' => 'Home',
            'navigation' => $this->paginator->generate(
                $taskModel,
                'partials/pagination',
                $this->request->get('p'),
                3
            ),
            'order' => $returnOrder
        ]);
    }

    /**
     * Error page 404
     */
    public function page404Action()
    {
        return $this->page404();
    }
}