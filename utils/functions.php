<?php

function start_session()
{
  $id = session_id();
  if ($id === "") {
    session_start();
  }
}

function is_logged_in()
{
  start_session();
  return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function is_admin()
{
  start_session();
  if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== "admin") {
    return false;
  }
  return true;
}

?>