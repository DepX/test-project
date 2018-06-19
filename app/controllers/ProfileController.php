<?php

namespace Acme\Controller;

class ProfileController extends ControllerBase
{
    /**
     * Profile
     */
    public function indexAction()
    {
        $this->view->render('profile/index', [
            'title' => 'Profile'
        ]);
    }
}