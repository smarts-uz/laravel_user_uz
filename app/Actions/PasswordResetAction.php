<?php

namespace App\Actions;
use TCG\Voyager\Actions\AbstractAction;

class PasswordResetAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Reset Password';
    }

    public function getIcon(){
        return 'voyager-tools';
    }
    public function getPolicy(){
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-danger pull-right',
            'data-toggle' => "modal",
            'data-target' => "#resetModal",
            'onclick' => "$('#exampleInputPassword1').val({{$this->data->id}})"
        ];
    }
    public function getDefaultRoute()
    {

    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'users';
    }
    public function shouldActionDisplayOnRow($row){
        return true;
    }
}
