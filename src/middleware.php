<?php
/**
 * @file middleware.php
 *
 * Set up some application middleware.
 */

if (!function_exists('adminer_object')) {
  function adminer_object()
  {

    class AdminerSoftware extends Adminer
    {
      function login($login, $password) {
        // Return true so that we don't need to log into the database.
        return TRUE;
      }
    }

    return new AdminerSoftware();
  }
}