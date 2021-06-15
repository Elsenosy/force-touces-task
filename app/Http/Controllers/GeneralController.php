<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class GeneralController extends Controller
{

    public $model;
    protected $pathImages = 'images/';
    protected $paginate = 20;
    protected $quality = 80;
    protected $encode = 'jpg';

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    /*************************************
    Start Queries Get Data
    ************************************/
    /**
     * Get data from Specific Model
     * @return mixed
     */
    public function getData()
    {
        return $this->model->orderBy('id', 'DESC');
    }


    /**
     * Get Specific Item By ID
     * @param $id
     * @return mixed
     */
    public function GetItem($id)
    {
        return $this->validateModel($id);
    }

    /*************************************
    Start Uploading Files
     ************************************/
    /**
     * Uploading Image
     * @param $file
     * @param $path
     * @param null $oldFile
     * @param int|null $width
     * @param int|null $height
     * @param int|null $thumbnailWidth
     * @param int|null $thumbnailHeight
     * @param bool $watermark
     * @return |null
     */
    public function uploadImage($file, $path, $oldFile = null, int $width = null, int $height = null, int $thumbnailWidth = null, int $thumbnailHeight = null, bool $watermark = false)
    {
        if($file) {
            // Rename File
            $rename = date('Ymdgis') . mt_rand(100, 1000000) . '.' .$file->getClientOriginalExtension();
            // Path File
            $fullPath = $file->storeAs($this->pathImages . $path, $rename, 'public_images');
            // Generate Image
            $image = Image::make($file);
            // If Watermark Equal True
            if($watermark === true) {
                $this->watermark($image);
            }
            // If Width and Height not Null
            if($width && $height) {
                // Resize image as specific width and height
                $image->resize($width, $height);
            } else if($width || $height) {
                // If need specific width and auto height
                $image->resize($width, $height, function ($aspect) {
                    $aspect->aspectRatio();
                });
            }
            // Save Image in the Full Path
            $image->save($fullPath, $this->quality ?? $this->quality, $this->encode);
            // Check If Exist thumbnail Image
            if($thumbnailWidth || $thumbnailHeight) {
                $this->thumbnailImage($file, $rename, $path, $thumbnailWidth, $thumbnailHeight, $watermark);
            }
            // Delete Old Files
            if($oldFile) {
                $this->deleteImage($oldFile);
            }
            return $fullPath;
        }
        return $oldFile;
    }

    // Generate Water Mark Inside Main Image
    private function watermark($image = null, $thumbnailImage = null)
    {
        $watermark = public_path('images/watermarks/logo.png');
        if($image)
            $image->insert($watermark, 'center');
        if($thumbnailImage)
            $thumbnailImage->insert($watermark, 'center');
    }

    // Generate Thumbnail Image
    private function thumbnailImage($file, $rename, $path, $width, $height, $watermark) {
        // Generate Thumbnail Image
        $thumbnailImage = Image::make($file);
        // If Watermark Equal True
        if($watermark === true) {
            $this->watermark(null, $thumbnailImage);
        }
        $fullThumbnailPath = $file->storeAs($this->pathImages . 'thumbnail/' . $path, $rename, 'public_images');
        // If Thumbnail Width and Height not Null
        if($width && $height) {
            // Resize Thumbnail image as specific width and height
            $thumbnailImage->resize($width, $height);
        } else if($width || $height) {
            // If need specific width and auto height
            $thumbnailImage->resize($width, $height, function ($ratio) {
                $ratio->aspectRatio();
            });
        }
        // Save Thumbnail Image in Path Thumbnail
        $thumbnailImage->save($fullThumbnailPath, $this->quality, $this->encode);
    }
    /*************************************
    End Uploading Files
     ************************************/
    /**
     * Get User Login APi
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function userApi()
    {
        return auth('api')->user();
    }

    /**
     * Delete Images from folders
     * @param $image, array
     * @return bool
     */
    public function deleteImage($image)
    {
        if($image) {
            if(is_array($image)) {
                foreach ($image as $img) {
                    if(!is_null($img)) {
                        // Delete Image from images folder
                        File::delete($image);
                        // Delete Thumbnail Image from Thumbnail folder
                        File::delete($this->pathImages . 'thumbnail' . substr($img, strpos($img, '/', 6)));
                    }
                }
            } else {
                // Delete Image from images folder
                File::delete($image);
                // Delete Thumbnail Image from Thumbnail folder
                File::delete($this->pathImages . 'thumbnail' . substr($image, strpos($image, '/', 6)));
            }
        }

        return true;
    }

    /**
     * Api response functions 
     */

    protected function returnError($message){
        return $this->resultResponse(false, $message, null, 203);
    }

    protected function returnSuccess($message, $payload){
        return $this->resultResponse(true, $message, $payload, 200);
    }

    protected function resultResponse($status=false, $message="", $payload=[], $code=200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'payload' => $payload
        ], $code);
    }

    
}
