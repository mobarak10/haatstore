<?php


namespace App\Helpers;


class PaginationSerial
{
    //get the serial number of pagination
    public static function serial($data)
    {
        return $data->perPage() * ($data->currentPage() - 1);
    }
}
