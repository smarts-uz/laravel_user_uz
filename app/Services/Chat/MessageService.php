<?php

namespace App\Services\Chat;

use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Str;

class MessageService
{
    public static function saveFile($file, $error)
    {
        $attachment = null;
        $attachment_title = null;
        // allowed extensions
        $allowed_images = Chatify::getAllowedImages();
        $allowed_files  = Chatify::getAllowedFiles();
        $allowed        = array_merge($allowed_images, $allowed_files);

        // check file size
        if ($file->getSize() < Chatify::getMaxUploadSize()) {
            if (in_array($file->getClientOriginalExtension(), $allowed)) {
                // get attachment name
                $attachment_title = $file->getClientOriginalName();
                // upload attachment and store the new name
                $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                $file->storeAs("public/" . config('chatify.attachments.folder'), $attachment);
            } else {
                $error->status = 1;
                $error->message = "File extension not allowed!";
            }
        } else {
            $error->status = 1;
            $error->message = "File size you are trying to upload is too large!";
        }
        return ['error' => $error, 'attachment' => $attachment, 'title' => $attachment_title];
    }
}
