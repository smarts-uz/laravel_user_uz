<?php

namespace App\Reports;

interface ALL
{
    public static function title();
    public static function data($startDate, $endDate);
    public static function condition($startDate, $endDate);
}
