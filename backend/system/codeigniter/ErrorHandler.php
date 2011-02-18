<?php
/* 
 * Define function to handle the error messages

// error handler function
function ciErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
    $exception = new ExceptionLog();
    $exception->user_id = 1;
    $exception->exception = $errno.': '.$errstr;
    $exception->dt_exception = date("Y-m-d H:i:s");
    $exception->trace = 'File: '.$errfile." \nLine: ".$errline;
    $exception->request = print_r($errcontext,true);

    $exception->save();

    return true;
}

set_error_handler("ciErrorHandler");

 */