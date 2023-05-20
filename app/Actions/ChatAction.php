<?php

namespace App\Actions;
use TCG\Voyager\Actions\AbstractAction;

class ChatAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Открыть чат';
    }

    public function getIcon()
    {
        return 'voyager-chat';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-info pull-right',
            'target' => '_blank'
        ];
    }

    public function getDefaultRoute()
    {
        return url('chat/'.$this->data->id);
    }

    public function shouldActionDisplayOnDataType()
    {
        if (env('ChatAction') === true){
            return $this->dataType->slug === 'users';
        }

        return $this->dataType->slug === '';
    }

    public function shouldActionDisplayOnRow($row){
        return true;
    }
}
