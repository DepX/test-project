<?php

namespace Acme\Controller;

use Acme\Model\Image as ImageModel;
use Acme\Model\Task as TaskModel;
use Acme\Service\Image as ImageService;

class TaskController extends ControllerBase
{
    /**
     * Task card
     *
     * @param $id
     * @return mixed
     */
    public function indexAction($id)
    {
        $taskModel = TaskModel::find([
            'conditions' => 'id = :id:',
            'bind' => [
                'id' => $id
            ]
        ])->getFirst();

        if (!$taskModel) {
            return $this->page404();
        }

        $this->render('task/index', [
            'title' => 'Task card',
            'taskModel' => $taskModel
        ]);
    }

    /**
     * Add task
     *
     * @throws \Exception
     */
    public function addAction()
    {
        $formErrorMessage = '';

        if ($this->request->isPost()) {
            if ($this->request->hasPost('user_name') && $this->request->hasPost('email') && $this->request->hasPost('message')) {
                $imageId = null;
                foreach ($this->request->getUploadedFiles() as $file) {
                    if ($file->getSize() != 0) {
                        $imageService = new ImageService();
                        $uploadFile = $imageService->upload($file);

                        if ($uploadFile !== false) {
                            $imageModel = ImageModel::insert([
                                'file' => $uploadFile['file_name'],
                                'created_at' => date('Y-m-d H:i:s', $uploadFile['created_at'])
                            ]);
                            $imageService->resize($imageModel, 320, 240);

                            if ($imageModel) {
                                $imageId = $imageModel->id;
                            }
                        }
                    }
                }

                $taskModel = TaskModel::insert([
                    'user_name' => $this->request->getPost('user_name'),
                    'email' => $this->request->getPost('email'),
                    'message' => $this->request->getPost('message'),
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'image_id' => $imageId
                ]);

                if (!empty($taskModel->id)) {
                    $this->response->redirect([
                        'for' => 'task',
                        'id' => $taskModel->id
                    ]);
                } else {
                    $formErrorMessage = 'Error while adding data';
                }
            } else {
                $formErrorMessage = 'Fill in required fields';
            }
        }

        $this->render('task/add', [
            'title' => 'Add task',
            'formErrorMessage' => $formErrorMessage
        ]);
    }

    /**
     * Edit task
     *
     * @param $id
     * @throws \Exception
     */
    public function editAction($id)
    {
        // check user
        if (!$this->security->getUser()) {
            return $this->page404();
        }

        $taskModel = TaskModel::find([
            'conditions' => 'id = :id:',
            'bind' => [
                'id' => $id
            ]
        ])->getFirst();

        if (!$taskModel) {
            return $this->page404();
        }

        $formErrorMessage = '';

        $field_createdAt = $this->request->getPost('created_at');

        if ($this->request->isPost()) {
            if (
                $this->request->hasPost('user_name') &&
                $this->request->hasPost('email') &&
                $this->request->hasPost('message') &&
                $this->request->hasPost('created_at') &&
                !empty($field_createdAt['date']) &&
                !empty($field_createdAt['time'])
            ) {
                $imageId = $taskModel->image_id;
                foreach ($this->request->getUploadedFiles() as $file) {
                    if ($file->getSize() != 0) {
                        $imageService = new ImageService();
                        $uploadFile = $imageService->upload($file);

                        if ($uploadFile !== false) {
                            $imageModel = ImageModel::insert([
                                'file' => $uploadFile['file_name'],
                                'created_at' => date('Y-m-d H:i:s', $uploadFile['created_at'])
                            ]);
                            $imageService->resize($imageModel, 320, 240);

                            if ($imageModel) {
                                $imageId = $imageModel->id;
                            }
                        }
                    }
                }

                $taskModel = TaskModel::update([
                    'conditions' => 'id = :id:',
                    'bind' => [
                        'id' => $id
                    ],
                    'new_values' => [
                        'user_name' => $this->request->getPost('user_name'),
                        'email' => $this->request->getPost('email'),
                        'message' => $this->request->getPost('message'),
                        'status' => ($this->request->getPost('status') == false) ? 0 : 1,
                        'created_at' => $field_createdAt['date'] . ' ' . $field_createdAt['time'] . ':00',
                        'image_id' => $imageId
                    ]
                ]);

                if ($taskModel) {
                    $this->response->redirect([
                        'for' => 'task',
                        'id' => $id
                    ]);
                } else {
                    $formErrorMessage = 'Error while adding data';
                }
            } else {
                $formErrorMessage = 'Fill in required fields';
            }
        }

        $this->render('task/edit', [
            'title' => 'Edit task',
            'taskModel' => $taskModel,
            'formErrorMessage' => $formErrorMessage
        ]);
    }

    /**
     * Delete task
     *
     * @param $id
     */
    public function deleteAction($id)
    {
        // check user
        if (!$this->security->getUser()) {
            return $this->page404();
        }

        $taskModel = TaskModel::find([
            'conditions' => 'id = :id:',
            'bind' => [
                'id' => $id
            ]
        ])->getFirst();

        if (!$taskModel) {
            return $this->page404();
        }

        if ($this->request->get('delete') == 'y') {
            $taskModel = TaskModel::delete([
                'conditions' => 'id = :id:',
                'bind' => [
                    'id' => $id
                ]
            ]);
            $this->response->redirect([
                'for' => 'home'
            ]);
        }

        $this->render('task/delete', [
            'title' => 'Delete task',
            'taskModel' => $taskModel
        ]);
    }
}