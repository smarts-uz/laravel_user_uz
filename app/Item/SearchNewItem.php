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

    public $user_name;
    public $user_image;
    public $user_email;

    public $category_icon;
    public $response_count;
    public $creator_name;

}
