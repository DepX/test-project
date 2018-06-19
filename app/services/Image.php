<?php

namespace Acme\Service;

use claviska\SimpleImage;
use Simple\Http\Request\File;

/**
 * Class Image
 * @package Simple\Service
 */
class Image
{
    /**
     * @var \Acme\Model\Image
     */
    protected $imageModel;

    /**
     * @param \Simple\Http\Request\File $file
     * @return array|bool
     */
    public function upload($file)
    {
        if ($file instanceof File) {

        } else {
            return false;
        }

        $type = explode('/', $file->getType());
        if (@$type[0] == 'image') {
            $created_at = date('U');

            // создание директорий
            $uploadDir = DIR_PUBLIC;
            $uploadDir .= DIRECTORY_SEPARATOR . 'files';
            $uploadDir .= DIRECTORY_SEPARATOR . 'images';
            $uploadDir .= DIRECTORY_SEPARATOR . date('Y', $created_at);
            $uploadDir .= DIRECTORY_SEPARATOR . date('m', $created_at);
            $uploadDir .= DIRECTORY_SEPARATOR . date('d', $created_at);
            if (!file_exists($uploadDir))
                mkdir($uploadDir, 0777, true);

            if ($type[1] == 'jpeg') {
                $file_extension = 'jpg';
            } elseif ($type[1] == 'bmp' || $type[1] == 'x-ms-bmp') {
                return false;
            } else {
                $file_extension = $type[1];
            }

            $new_file_name = date('U') . '_' . rand(100, 500) . '.' . $file_extension;
            $file->moveTo($uploadDir . DIRECTORY_SEPARATOR . 'original_' . $new_file_name);

            return [
                'created_at' => $created_at,
                'file_name' => $new_file_name,
                'upload_dir' => $uploadDir
            ];
        }

        return false;
    }

    /**
     * Resize image
     *
     * @param $image
     * @param $width
     * @param $height
     * @return null|string
     * @throws \Exception
     */
    public function resize($image, $width, $height)
    {
        $path_file = $this->getPathBySize($image, $width, $height);

        if (($width || $height) && !file_exists(DIR_PUBLIC . $path_file) && file_exists(DIR_PUBLIC . $this->getWebDir() . 'original_' . $image->file)) {

            $simpleImage = new SimpleImage();
            $simpleImage->fromFile(DIR_PUBLIC . $this->getWebDir() . 'original_' . $image->file);

            if ($width && $height) {
                $simpleImage->thumbnail($width, $height, 'center');
                //$simpleImage->resize($width, $height);
            } elseif ($width) {
                $simpleImage->resize($width, null);
            } elseif ($height) {
                $simpleImage->resize(null, $height);
            }

            $simpleImage->toFile(DIR_PUBLIC . $path_file);
        }

        return $path_file;
    }

    /**
     * @param $image
     * @param $width
     * @param $height
     * @return null|string
     */
    public function getPathBySize($image, $width, $height)
    {
        if ($image == null) {
            return null;
        }
        $this->imageModel = $image;

        $path_file = '';
        if ($width) $path_file .= 'W' . $width;
        if ($height) $path_file .= 'H' . $height;
        if ($path_file == '') {
            $path_file = 'original_' . $image->file;
        } else {
            $path_file = $path_file . '_' . $image->file;
        }

        return $this->getWebDir() . $path_file;
    }

    /**
     * Generate path
     *
     * @return string
     */
    public function getWebDir()
    {
        $uploadDir = '/files/images';
        $uploadDir .= '/' . $this->imageModel->getCreatedAt()->format('Y');
        $uploadDir .= '/' . $this->imageModel->getCreatedAt()->format('m');
        $uploadDir .= '/' . $this->imageModel->getCreatedAt()->format('d');
        $uploadDir .= '/';
        return $uploadDir;
    }
}