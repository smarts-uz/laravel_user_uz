<?php
namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class  ActiveAction extends AbstractAction {
    public function getTitle(){

        return  $this->data->is_active?"Active":"NoActive";
    }
    public function getIcon(){
        return 'voyager-download';
    }
    public function getPolicy(){
        return 'read';
    }

    public function getAttributes()
    {
        $color = $this->data->is_active?"success":"danger";
        return [
            'class' => "btn active btn-sm btn-$color pull-right width-active",
        ];
    }
    public function getDefaultRoute()
    {

        return route('voyagerUser.activity', ['user' => $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'users' && auth()->user()->hasPermission("change_activeness");
    }
    public function shouldActionDisplayOnRow($row){
        return true;
    }
}
