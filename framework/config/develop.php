<?php
error_reporting(E_ALL ^ E_NOTICE);

ini_set('display_startup_errors', 'on');
ini_set('display_errors', 'on');
ini_set('html_errors', 'on');
ini_set('docref_root', 'http://php.net/manual/en/');
ini_set('log_errors', 'off');

// debug
$debug = true;
$debug_db_queries_count = 0;
$debug_heaviest_query_time = 0;
set_exception_handler('my_exception_handler');

// paths:
$config['path']['root']    = $_SERVER['DOCUMENT_ROOT'];
$config['path']['cache']   = $config['path']['root'] . '/cache';
$config['path']['classes'] = $config['path']['root'] . '/classes';
$config['path']['files']   = $config['path']['root'] . '/files';


/**
 * Uncaught exception handler
 *
 * @param Exception $Exception
 */
function my_exception_handler(Exception $Exception)
{
    echo '<hr><h3 style="color: red;">Exception</h3>';
    echo '<table cellpadding="3">';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">Exception text:</td>';
    echo '<td>' . $Exception->getMessage() . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">Exception source:</td>';
    echo '<td>' . $Exception->getFile() . ', line ' . $Exception->getLine() . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">$_REQUEST:</td>';
    echo '<td>'; print_r_ex($_REQUEST); echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">$_SESSION:</td>';
    echo '<td>'; print_r_ex($_SESSION); echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">Exception trace:</td>';
    echo '<td>'; print_r_ex($Exception->getTrace()); echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">debug_backtrace():</td>';
    echo '<td>'; print_r_ex(debug_backtrace()); echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background-color: #ddd; text-align: right;">$_SERVER:</td>';
    echo '<td>'; print_r_ex($_SERVER); echo '</td>';
    echo '</tr>';
    echo '</table>';
}


