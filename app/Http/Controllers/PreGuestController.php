<?php

namespace App\Http\Controllers;

use Input;
use App\PreGuestUser;
use App\Http\Helpers\FileHelper;
use App\AvailableFacility;
use App\FacilityImages;
use Carbon\Carbon;
use File;

class PreGuestController extends Controller
{

    public function saveGuestUser()
    {
        $data = Input::all();
        $preUser = new PreGuestUser();
        $preUser->full_name = $data['name'];
        $preUser->email = $data['email'];
        $preUser->phone = $data['phone'];
        $preUser->created_at = Carbon::now();
        $preUser->updated_at = Carbon::now();
        $response = [
            "success" => $preUser->save()
        ];
        return $response;
    }

    public function addImages()
    {
        $facilities = AvailableFacility::all();
        return view('views/addimages', ['facilities' => $facilities]);
    }

    /**
     * Uploading media for ticker 
     * 
     * @return json
     */
    public function uploadMedia()
    {
        $fileInputName = Input::get('mediatype');
        $params = Input::file($fileInputName);
        $response = array('valid' => 0, 'fileName' => null, 'error' => null);
        $fileHelper = new FileHelper($params['image']);
        $fileHelper->setFileName(time() . rand(1, 50));
        $isUpload = $fileHelper->upload('temp');
        if ($isUpload) {
            $response['valid'] = 1;
            $response['fileName'] = $fileHelper->getFileName();
        }

        return json_encode($response);
    }

    public function upload(\Illuminate\Http\Request $request)
    {
        $files = $request->get('fileName');
        $facility = $request->get('facility');
        $vendorId = $request->get('vendor');
        $fileNamesArray =  array();
        if ($files) {
            $fileNames = explode(",", $files);
            foreach ($fileNames as $file) {
                $vendorUploadPath =  env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($vendorId);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "facility_images";

                /* Create Upload Directory If Not Exists */
                if (!file_exists(public_path().$vendorImageUploadPath)) {
                    $filePath = 'uploads/userdata/vendor/'.sha1($vendorId). "/" . "facility_images";
                    File::makeDirectory($filePath, $mode = 0777, true, true);
                    chmod($filePath, 0777);
                    chmod($filePath, 0777);
                }

                $fileNewName = explode(".",$file);
                $extension = end($fileNewName);
                $fileNamesArray[] = $file;
                $fileHelper = new FileHelper();
                $fileHelper->sourceFilename = $file;
                $fileHelper->sourceFilepath = 'uploads/temp/';
                $fileHelper->destinationPath = public_path('uploads/userdata/vendor/'.sha1($vendorId) . "/" . "facility_images". "/");
                $fileHelper->resizeImage('ticker', true);
                
            }
            
            foreach($fileNamesArray as $filesName) {
                $facilityImage = new FacilityImages;
                $facilityImage->image_name = $filesName;
                $facilityImage->available_facility_id = $facility;
                $facilityImage->created_at = date("Y-m-d H:i:s");
                $facilityImage->updated_at = date("Y-m-d H:i:s");
                $facilityImage->save();
                
            }
        }
        
       return redirect(url('add/facility/images'));
    }

}