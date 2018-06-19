<?php

namespace Acme\Model;

class Image extends ModelBase
{
    public $created_at;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return new \DateTime($this->created_at);
    }

    /**
     * Returns table name
     *
     * @return null|string
     */
    public function getSource()
    {
        return 'image';
    }
}