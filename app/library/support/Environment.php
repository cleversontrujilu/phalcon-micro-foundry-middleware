<?php
namespace Support;

class Environment
{
      private $Env;

      public function __construct()
      {
          if(is_file(APP_PATH . "/config/environment.php"))
              $this->Env = require APP_PATH . "/config/environment.php";
      }

      public function returnConfigFile($file = false)
      {
          if(!$file)
              return false;

          if(!empty($this->Env))
              if(is_file(APP_PATH . "/config/" . $this->Env . "//" . $file))
                    return include APP_PATH . "/config/" . $this->Env . "//" . $file;

          return include APP_PATH . "/config//" . $file;
      }
}
