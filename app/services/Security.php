<?php

namespace Acme\Service;

use Acme\Model\User as UserModel;
use Acme\Model\UserToken as UserTokenModel;
use SimpleMvc\Di;
use SimpleMvc\DI\InjectionInterface;

class Security implements InjectionInterface
{
    private $di;

    private $user = null;

    /**
     * Security constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * @param \SimpleMvc\DI\DefaultFactory $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @return \SimpleMvc\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * Get current user
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Initializes service
     * Verification and authorization of the user
     *
     * @return $this
     */
    public function initialize()
    {
        $this->di = Di::getDefault();

        $cookies = $this->di->getService('cookies');
        $cookieToken = $cookies->get('token');

        // check token
        $userTokenModel = UserTokenModel::find([
            'conditions' => 'token = :token:',
            'bind' => [
                'token' => $cookieToken
            ]
        ])->getFirst();

        if ($userTokenModel) {
            // check user by token
            $userModel = UserModel::find([
                'conditions' => 'id = :id:',
                'bind' => [
                    'id' => $userTokenModel->user_id
                ]
            ])->getFirst();

            if ($userModel) {
                // auth
                $this->user = $userModel;
            }
        }

        return $this;
    }

    /**
     * Authorization user
     *
     * @param $login
     * @param $password
     * @param bool $remember
     * @return array
     */
    public function auth($login, $password, $remember = false)
    {
        $crypt = $this->getDi()->getService('crypt');

        $userModel = UserModel::find([
            'conditions' => 'login = :login:',
            'bind' => [
                'login' => $login
            ]
        ])->getFirst();
        if (!$userModel) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        if (!$crypt->checkHash($password, $userModel->password)) {
            return [
                'success' => false,
                'message' => 'Password incorrect'
            ];
        }

        $token = md5($userModel->id . '_' . time() . 'L#mj3:W)<:V4z`f06;pi2#|9ZXrI%hDk=,T%^');

        $userTokenModel = UserTokenModel::insert([
            'user_id' => $userModel->id,
            'token' => $token
        ]);
        if (!empty($userTokenModel->id)) {
            $cookies = $this->getDi()->getService('cookies');
            $cookies->set('token', $userTokenModel->token, time() + 3600 * 24 * 365);

            return [
                'success' => true,
                'message' => ''
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error while adding data'
            ];
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $cookies = $this->getDi()->getService('cookies');
        $cookies->delete('token');
    }
}