<?php

function session_initialization($session_id, $key, $iv, $admin, $course_list){
    session_set_cookie_params(3600);
    session_start();
    $_SESSION["session_id"] = $session_id;
    $_SESSION["key"] = $key;
    $_SESSION["iv"] = $iv;
    $_SESSION["admin"] = $admin;
    $_SESSION["info_text"] = "";
    $_SESSION["course_list"] = $course_list;
}


//if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
//    echo "HIIII\n";
//}
?>