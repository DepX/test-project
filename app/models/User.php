<?php

namespace Acme\Model;

class User extends ModelBase
{
    /**
     * Returns table name
     *
     * @return null|string
     */
    public function getSource()
    {
        return 'user';
    }
}