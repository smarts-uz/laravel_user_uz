<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $attachment = [
            'path' => null,
            'type' => null
        ];
        if(isset($this->attachment)){
            $attachmentOBJ = json_decode($this->attachment);
            $attachment['path'] = url('/storage/attachments') . '/' . $attachmentOBJ->new_name;
            $ext = pathinfo($attachmentOBJ->new_name, PATHINFO_EXTENSION);
            $attachment['type'] = in_array($ext,config('chatify.attachments.allowed_images')) ? 'image' : 'file';
        }
        return [
            'id' => $this->id,
            'from_id' => $this->from_id,
            'to_id' => $this->to_id,
            'message' => $this->body,
            'attachment' => $attachment,
            'seen' => $this->seen,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
