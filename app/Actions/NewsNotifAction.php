<?php

namespace App\Actions;
use TCG\Voyager\Actions\AbstractAction;

class NewsNotifAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Уведомить';
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
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }
    public function getDefaultRoute()
    {
        return route('blogNews', ['newsId' => $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'blog-new';
    }
    public function shouldActionDisplayOnRow($row){
        return true;
    }
}
