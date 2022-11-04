<?php

namespace App\Actions;
use TCG\Voyager\Actions\AbstractAction;

class InfoAction extends AbstractAction
{
    public function getTitle()
    {
        return 'User Info';
    }

    public function getIcon(){
        return 'voyager-info-circled';
    }
    public function getPolicy(){
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right',
        ];
    }
    public function getDefaultRoute()
    {
        return route('voyagerUser.activity', ['user' => $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'users';
    }
    public function shouldActionDisplayOnRow($row){
        return true;
    }
}
