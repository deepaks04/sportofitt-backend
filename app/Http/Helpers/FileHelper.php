<?php

/*
  |@author: Alankar More
  |
  |--------------------------------------------------------------------------
  | Helper for file operations such as renaming,writting,uploding etc.
  |--------------------------------------------------------------------------
  | Each helper function will provide some basic functionalities.
  |
 */
namespace App\Http\Helpers;

use Config;
use Extlib\ImageOptimizer as ImageOptimizer;
use app\models\MediaMaster;

class FileHelper
{

    /**
     *
     * Setting new file name
     *
     * @var string
     */
    public $_fileName;

    /**
     * Uploaded file instance
     *
     * @var Object uploaded file
     */
    public $_file;

    /**
     * Source file name
     * @var string
     */
    public $sourceFilename;

    /**
     * Soruce file path
     * 
     * @var string
     */
    public $sourceFilepath;

    /**
     * Destination path to save file
     * 
     * @var string
     */
    public $destinationPath;

    /**
     * If we need to provide new name to image
     *  
     * @var string
     */
    public $newNameForFile = null;

    /**
     * This is the dimensions for the user entity
     * 
     * @var dimensions for user profile images. 
     */
    public $user = array('small' => array('262X196'));
    public $ticker = array('small' => array('555X415'));
    protected static $userDirs = array('image', 'audio', 'video', 'thumbnail');

    /**
     * Setting instance of uploaded file
     *
     */
    public function __construct($fileInstance = null)
    {
        if (!empty($fileInstance)) {
            $this->_file = $fileInstance;
        }
    }

    /**
     * uploading file to destination
     *
     * @return boolean
     * throw exception Exception
     */
    public function upload($uploadPath)
    {
        try {
            return $this->_file->move(public_path('uploads/' . $uploadPath), $this->getFileName());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Set user defined file name
     *
     * @var string $filename
     */
    public function setFileName($filename)
    {
        $this->_fileName = $filename . "." . $this->_getFileExtension();
    }

    /**
     * Get file name. if it has been changed
     *
     * @return string
     */
    public function getFileName()
    {
        return ($this->_fileName) ? $this->_fileName : $this->_file->getClientOriginalName();
    }

    /**
     * Get file extension for uploaded file
     *
     * @return string
     */
    public function _getFileExtension()
    {
        return $this->_file->getClientOriginalExtension();
    }

    /**
     * removing file
     *
     * @return boolean
     */
    public function removeFile($fileName)
    {
        $file = public_path($fileName);
        return (file_exists($file)) ? unlink($file) : false;
    }

    /**
     * Crropping image according to the provided image dimensions.
     * 
     * @param array $imageDimensions
     * @param string $key
     * @return string thumbnail image name
     */
    private function _cropImage($imageDimensions, $key)
    {
        $sourceImage = $this->sourceFilepath . $this->sourceFilename;
        $widthHeight = $imageDimensions[0] . 'X' . $imageDimensions[1];

        $widthAuto = $this->sourceFilepath . 'thumb_width_auto_' . $imageDimensions[0] . '_' . $this->sourceFilename;
        $thumb = $this->sourceFilepath . 'thumb_' . $widthHeight . '_' . $this->sourceFilename;
        $command = '/usr/bin/convert ' . $sourceImage . ' -resize ' . $imageDimensions[0] . ' x ' . $widthAuto;
        if ($key == 'small') {
            $command = '/usr/bin/convert ' . $sourceImage . ' -resize x' . $imageDimensions[1] . ' ' . $widthAuto;
        }
        exec($command);

        $newImageSize = getimagesize($widthAuto);
        if ($key == 'small') {
            if ($newImageSize[0] >= $imageDimensions[0]) {
                $ratio = round(($newImageSize[0] - $imageDimensions[0]) / 2);
                // if width >= expected width and height is not maching.
                $command = '/usr/bin/convert ' . $widthAuto . ' -crop ' . $imageDimensions[0] . 'x' . $imageDimensions[1] . '+' . $ratio . ' ! -quality 100' . ' ' . $thumb;
                exec($command);
            } else {
                copy($widthAuto, $thumb);
            }
        } else {
            if ($newImageSize[1] >= $imageDimensions[1]) {
                $ratio = round(($newImageSize[1] - $imageDimensions[1]) / 2);
                // if width >= expected width and height is not maching.
                $command = '/usr/bin/convert ' . $widthAuto . ' -shave 0x' . $ratio . ' -quality 100' . ' ' . $thumb;
                exec($command);
            } else {
                copy($widthAuto, $thumb);
            }
        }

        unlink($widthAuto);

        return 'thumb_' . $widthHeight . '_' . $this->sourceFilename;
    }

    /**
     * Resizing image as per the given image ratios.
     * 
     * @param string $entityName
     * @param boolean $checkDir
     */
    public function resizeImage($entityName, $checkDir = false)
    {
        $sourceImage = $this->sourceFilepath . $this->sourceFilename;
        if ($checkDir) {
            if (!file_exists($this->destinationPath)) {
                mkdir($this->destinationPath, 0777, TRUE);
                chmod($this->destinationPath, 0777);
            }
        }

        $originalImageSize = getimagesize($sourceImage);
        foreach ($this->$entityName as $key => $dimension) {
            foreach ($dimension as $value) {
                $imageDimensions = explode("X", $value);
                if ($originalImageSize[0] >= $imageDimensions[0] || $originalImageSize[1] >= $imageDimensions[1]) {
                    $thumbnail = $this->_cropImage($imageDimensions, $key);
                    $fileName = null;
                    if (!empty($thumbnail)) {
                        $fileName = $this->destinationPath . $thumbnail;
                        rename($this->sourceFilepath . $thumbnail, $fileName);
                    } else {
                        $fileName = $this->sourceFilepath . 'thumb_' . $value . '_' . $this->sourceFilename;
                        copy($sourceImage, $fileName);
                    }
                    //$this->getImageOptimizerInstance()->optimize($fileName);
                } else {
                    copy($sourceImage, $this->destinationPath . $this->sourceFilename);
                }
            }
        }

        rename($sourceImage, $this->destinationPath . $this->sourceFilename);
        //$this->getImageOptimizerInstance()->optimize($this->destinationPath . $this->sourceFilename);
    }

    /**
     * Get file size according to the file path
     * 
     * @param string $filePath
     * @param boolean $inKB
     * @return mixed integer|string
     */
    public static function getFileSize($filePath, $inKB = true)
    {
        $size = 0;
        if (!empty($filePath) && file_exists($filePath)) {
            $size = filesize($filePath);
            if ($inKB) {
                $size = number_format(($size / 1024), 2);
            }
        }

        return $size;
    }

    /**
     * Get file dimension according to the file path in terms of width X height
     * This is applicable for image only
     * 
     * @param string $filePath
     * @return string
     */
    public static function getFileDimension($filePath)
    {
        $dimension = '0X0';
        if (!empty($filePath) && file_exists($filePath)) {
            $imageSize = getimagesize($filePath);
            if (!empty($imageSize)) {
                $dimension = $imageSize[0] . 'X' . $imageSize[1];
            }
        }
        return $dimension;
    }

    /**
     * Get file type according to the file path
     * 
     * @param string $filePath
     * @return string
     */
    public static function getFileType($filePath)
    {
        $fileType = 'unknown';
        if (!empty($filePath)) {
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        }

        return $fileType;
    }

    /**
     * Checking the media file path and sending the respective image or video
     * thumbnail
     * 
     * @param boolean $bigImage
     * @return string | boolean
     */
    public static function getMediaFilePathForView($media, $bigImage = false, $isOriginal = false)
    {
        if (!empty($media->user->id)) {
            $source = 'uploads/' . $media->user->id . "/" . $media->type;

            if ($isOriginal === true) {
                if ('app\models\Ticker' == $media->entity_type || 'app\models\TickerTicks' == $media->entity_type) {
                    $tickerId = $media->entity->id;
                    if ('app\models\TickerTicks' == $media->entity_type) {
                        $tickerId = $media->entity->ticker->id;
                        return $source . '/' . $tickerId . '/' . $media->file_name;
                    }
                } else {
                    return $source . '/' . $media->file_name;
                }
            }

            if ('app\models\Ticker' == $media->entity_type || 'app\models\TickerTicks' == $media->entity_type) {
                $tickerId = $media->entity->id;
                if ('app\models\TickerTicks' == $media->entity_type) {
                    $tickerId = $media->entity->ticker->id;
                }
                $source .= self::getMediaFileByType($media->type, $media->file_name, $bigImage, $tickerId);
            } else {
                $source .= self::getMediaFileByType($media->type, $media->file_name, $bigImage);
            }

            $filePath = public_path($source);
            if (file_exists($filePath)) {
                return $source;
            }
        }

        return false;
    }

    /**
     * Get media file name by type
     * 
     * @param string $type
     * @param string $fileName
     * @param boolean $bigImage
     * @param integer $tickerId
     * @return string
     */
    public static function getMediaFileByType($type, $fileName, $bigImage = false, $tickerId = null)
    {
        $file = "/" . $fileName;
        switch ($type) {
            case 'video':
                if (!empty($tickerId)) {
                    $file = "/" . $tickerId . "/" . $fileName;
                }
                break;

            case 'audio':
                if (!empty($tickerId)) {
                    $file = "/" . $tickerId . "/" . $fileName;
                }
                break;

            case 'image':
                if (!empty($tickerId)) {
                    $file = "/" . $tickerId . "/thumb_300X300_" . $fileName;
                    if ($bigImage) {
                        $file = "/" . $tickerId . "/" . $fileName;
                    }
                } else {
                    $file = "/thumb_300X300_" . $fileName;
                    if ($bigImage) {
                        $file = "/" . $fileName;
                    }
                }
                break;
        }

        return $file;
    }

    /**
     * Is media Directories are exists for the user 
     * 
     * @param integer $userId
     */
    public static function isMediaDirectoriesExistsForUser($userId)
    {
        $path = public_path() . "/uploads";
        if (!file_exists($path . "/" . $userId)) {
            mkdir($path . "/" . $userId, 0777, true);
            chmod($path . "/" . $userId, 0777);
            foreach (self::$userDirs as $dir) {
                mkdir($path . "/" . $userId . "/" . $dir, 0777, TRUE);
                chmod($path . "/" . $userId . "/" . $dir, 0777);
            }
        }
    }

    /**
     * Moving the file from one path to another path 
     * 
     * @param string $oldPath
     * @param string $newPath
     */
    public function moveFile($oldPath, $newPath)
    {
        if (file_exists($oldPath)) {
            rename($oldPath, $newPath);
        }
    }

    /**
     * Converting normal image into blur image 
     * 
     * @param string $filePath
     * @param string $fileName
     * @param string $entityName
     */
    public function makeBlurImage($filePath, $fileName, $entityName)
    {
        $newImageName = 'blur_' . $fileName;
        $file = public_path($filePath);
        // convert circle_on_blue.png    -blur 0x8         circle_on_blue_blur.png
        $command = '/usr/bin/convert ' . $file . $fileName . ' -channel RGBA -blur 0x8 ' . $file . $newImageName;
        exec($command);

        foreach ($this->$entityName as $dimensions) {
            foreach ($dimensions as $dimension) {
                $command = '/usr/bin/convert ' . $file . "thumb_" . $dimension . "_" . $fileName . ' -channel RGBA  -blur 0x8 ' . $file . "blur_thumb_" . $dimension . "_" . $fileName;
                exec($command);
            }
        }
    }

    /**
     * Get where this media has been used that is for which ticker.
     * 
     * @param UserTickerMedia $media
     * @return string
     */
    public static function getWhereItIsUsedWith(MediaMaster $media)
    {
        $usedWith = 'NA';
        if ($media->entity_type == 'app\models\Ticker') {
            $usedWith = $media->entity->precoverage_title;
        }

        if ($media->entity_type == 'app\models\TickerTicks') {
            $usedWith = $media->entity->ticker->precoverage_title;
        }

        return $usedWith;
    }

    /**
     * Get image optimizer instance for optimizing png / jpeg / gif images.
     * 
     * @return Extlib\ImageOptimizer
     */
    public function getImageOptimizerInstance()
    {
        return new ImageOptimizer(array(
            ImageOptimizer::OPTIMIZER_OPTIPNG => Config::get('constants.imageoptimizer.optipng'), //your_path
            ImageOptimizer::OPTIMIZER_JPEGOPTIM => Config::get('constants.imageoptimizer.jpegoptim'), //your_path
            ImageOptimizer::OPTIMIZER_GIFSICLE => Config::get('constants.imageoptimizer.gifsicle')
        ));
    }

    /**
     * Checking is the provided mime type is for image.
     * 
     * @param string $mimeType
     * @return boolean
     */
    public static function isItImage($mimeType)
    {
        $imageMimeTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/bmp', 'image/gif');

        return in_array(strtolower($mimeType), $imageMimeTypes);
    }

    /**
     * Checking the provided mime type is for video.
     * 
     * @param string $mimeType
     * @return boolean
     */
    public static function isItVideo($mimeType)
    {
        $videoMimeTypes = array('video/x-flv', 'video/mp4', 'application/x-mpegURL', 'video/MP2T', 'video/3gpp', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv');

        return in_array(strtolower($mimeType), $videoMimeTypes);
    }

    /**
     * 
     * @param string $mimeType
     * @return boolean
     */
    public static function isItAudio($mimeType)
    {
        $videoMimeTypes = array('audio/mpeg3', 'audio/x-mpeg-3', 'video/mpeg', 'video/x-mpeg', 'audio/mpeg', 'video/mpeg');

        return in_array(strtolower($mimeType), $videoMimeTypes);
    }

}