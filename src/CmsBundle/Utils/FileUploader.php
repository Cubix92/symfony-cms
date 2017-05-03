<?php

namespace CmsBundle\Utils;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class FileUploader
{
    private $targetDir;

    /**
     * @param $uploadPath
     *
     * @return string
     */
    protected function generatePath($uploadPath) {
        return $this->targetDir . $uploadPath . '/';
    }

    /**
     * FileUploader constructor.
     *
     * @param string $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir . '/../web';
    }

    /**
     * Uploads file to server
     *
     * @param UploadedFile $file
     * @param string|null $uploadPath
     * @param string $customName
     *
     * @return string
     */
    public function upload(UploadedFile $file, $uploadPath, $customName = '')
    {
        $filePath = $this->generatePath($uploadPath);
        $fileName = $customName ? $customName : md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move($filePath, $fileName);
        } catch(FileException $e) {
            return false;
        }

        return $fileName;
    }

    /**
     * Checks if file exists
     *
     * @param $filename
     * @param null $uploadPath
     * @return bool|File
     */
    public function check($filename, $uploadPath) {

        $path = $this->generatePath($uploadPath);

        try {
            $file = new File($path . $filename);
        } catch(FileException $e) {
            return false;
        }

        return $file;
    }

    /**
     * Removes file from server
     *
     * @param string $filename
     * @param null $uploadPath
     *
     * @return boolean
     */
    public function remove($filename, $uploadPath) {
        $path = $this->generatePath($uploadPath);

        try {
            $finder = new Finder();
            $finder->in($path)->name('*' . $filename);
        } catch(\InvalidArgumentException $e) {
            return false;
        }

        foreach($finder as $file) {
            unlink($file);
        }

        return true;
    }

}