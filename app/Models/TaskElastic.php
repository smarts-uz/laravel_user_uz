<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace App\Models;


use Elastic\ScoutDriverPlus\Searchable;

class TaskElastic extends Task
{
    use Searchable;
}
