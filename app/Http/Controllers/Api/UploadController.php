<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Upload a file.
     *
     * @param  \App\Http\Requests\Api\UploadRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(UploadRequest $request)
    {
        try {
            $file = $request->file('file');
            $type = $request->input('type');

            // Validate file type based on upload type
            if (!$this->isValidFileType($file, $type)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid file type for the specified upload type'
                ], 422);
            }

            // Generate a unique filename
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Determine storage path based on type
            $storagePath = $this->getStoragePath($type);

            // Store the file
            $path = $file->storeAs($storagePath, $filename, 'public');

            // Generate URL
            $url = Storage::url($path);

            $fileInfo = [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'path' => $path,
                'url' => $url,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'type' => $type,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $fileInfo,
                'message' => 'File uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Validate file type based on upload type.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $type
     * @return bool
     */
    private function isValidFileType($file, $type)
    {
        $allowedTypes = [
            'document' => ['pdf', 'doc', 'docx'],
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'medical_certificate' => ['pdf', 'jpg', 'jpeg', 'png'],
        ];

        if (!isset($allowedTypes[$type])) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $allowedTypes[$type]);
    }

    /**
     * Get storage path based on file type.
     *
     * @param  string  $type
     * @return string
     */
    private function getStoragePath($type)
    {
        $paths = [
            'document' => 'documents',
            'image' => 'images',
            'medical_certificate' => 'medical_certificates',
        ];

        return $paths[$type] ?? 'uploads';
    }
}
