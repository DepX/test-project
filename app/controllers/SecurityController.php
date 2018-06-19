<?php

namespace Acme\Controller;

class SecurityController extends ControllerBase
{
    /**
     * Login page
     */
    public function loginAction()
    {
        $formErrorMessage = '';

        if ($this->request->isPost()) {
            if ($this->request->hasPost('login') && $this->request->hasPost('password')) {
                $security = $this->security->auth(
                    $this->request->getPost('login'),
                    $this->request->getPost('password')
                );

                if ($security['success'] == false) {
                    $formErrorMessage = $security['message'];
                } else {
                    $this->response->redirect([
                        'for' => 'home'
                    ]);
                }
            } else {
                $formErrorMessage = 'Fields "Login" and "Password" in required';
            }
        }

        $this->view->render('security/login', [
            'title' => 'Sign in',
            'formErrorMessage' => $formErrorMessage
        ]);
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
        $this->security->logout();
        $this->response->redirect([
            'for' => 'home'
        ]);
    }
}