<?php

namespace App\Actions;
use TCG\Voyager\Actions\AbstractAction;

class CancelAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Отменить задачу';
    }

    public function getIcon()
    {
        return 'voyager-x';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-info pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyagerTask.cancel', ['task' => $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug === 'tasks' ;
    }

    public function shouldActionDisplayOnRow($row){
        return true;
    }
}
