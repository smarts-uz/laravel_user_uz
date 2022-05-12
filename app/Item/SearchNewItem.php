<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace App\Item;


use App\Models\ComplianceType;
use App\Models\Task;

class SearchNewItem
{
    public $id;
    public $name;

    /**
     * @var
     * Main Address
     */
    public $address_main;

    /**
     * @var
     * Additional Addresses
     */
    public $address_adds;

    public $budget;
    public $date_type;
    public $start_date;
    public $end_date;

    public $user_id;
    public $user_name;

    public $category_icon;
    public $response_count;

}