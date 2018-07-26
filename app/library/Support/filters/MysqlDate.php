<?php
namespace Support\Filters;

class MySQLDate
{
    public function filter($value)
    {
        $value = str_replace("/" , "-" , $value);
        return date("Y-m-d" , strtotime($value));
    }
}
