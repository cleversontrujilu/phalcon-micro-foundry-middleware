<?php
namespace Support;

use Phalcon\Filter;

class Filters
{

      private static $Filter;

      static public function initialize()
      {
          self::setFilter();
          self::addCustomFields();
          return new self();
      }

      private static function setFilter()
      {
          self::$Filter = new Filter();
      }

      private static function addCustomFields()
      {
          (self::$Filter)->add(
            'MySQLDate',
            new \Support\Filters\MySQLDate()
          );
      }

      public function getFilter()
      {
          return self::$Filter;
      }

}
