<?php

namespace Acme\Model;

class Task extends ModelBase
{
    public $image_id;
    public $created_at;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return new \DateTime($this->created_at);
    }

    /**
     * Get image path 320x240
     *
     * @return null|string
     */
    public function getImagePath()
    {
        if ($this->image_id > 0) {
            $imageModel = Image::find([
                'conditions' => 'id = :id:',
                'bind' => [
                    'id' => $this->image_id
                ]
            ])->getFirst();

            if ($imageModel) {
                $imageService = new \Acme\Service\Image();
                $image = $imageService->getPathBySize($imageModel, 320, 240);
                return $image;
            }
        }

        return '';
    }

    /**
     * Returns table name
     *
     * @return null|string
     */
    public function getSource()
    {
        return 'task';
    }
}