<?php

class CoralSession
{
  private static $opened;

  private static function open_for_read() {
    if (!isset(self::$opened)) {
       session_start();
       session_write_close();
       self::$opened = true;
     }
  }
  
  public static function get($key) {
    self::open_for_read();
    if (isset($_SESSION[$key])) {
      return $_SESSION[$key];
    } else {
      return null;
    }
  }
  
  public static function set($key, $value) {
    session_start();
    $_SESSION[$key] = $value;
    session_write_close();
    self::$opened = true;
  }
  
}

?>