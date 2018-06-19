<?php

namespace Acme\Model;

class UserToken extends ModelBase
{
    /**
     * Returns table name
     *
     * @return null|string
     */
    public function getSource()
    {
        return 'user_token';
    }
}