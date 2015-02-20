<?php
/*
 * Server config
 */

// errors off
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_startup_errors', 'off');
ini_set('display_errors', 'off');

// paths:
$config['path']['root']    = $_SERVER['DOCUMENT_ROOT'];
$config['path']['cache']   = $config['path']['root'] . '/cache';
$config['path']['classes'] = $config['path']['root'] . '/classes';
$config['path']['files']   = $config['path']['root'] . '/files';

