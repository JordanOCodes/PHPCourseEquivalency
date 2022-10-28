<?php
require __DIR__ . '/include/encryption_and_hashing.php';
require __DIR__ . '/include/connect_to_database.php';
require __DIR__ . '/include/session_handling.php';
session_start();
if (session_status() != PHP_SESSION_NONE and isset($_SESSION['session_id'])) {
    session_destroy();
//    $auth = decrypt_data(get_session_auth_code($_SESSION["session_id"]), $_SESSION['key'], $_SESSION["iv"]);
//    if ($auth == "Student") {
//        header("Location: student_root.php");
//        exit();
//    }
//    else if($auth == "Faculty") {
//        header("Location: faculty_root.php");
//        exit();
//    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student's Course Equivalency Form</title>
    <link rel="stylesheet" href="static/css/style.css">

</head>

<body>
<?php
function redirect_student_root(){
    header("Location: student_root.php", true, 301);
    exit();
}
function redirect_faculty_root(){
    header("Location: faculty_root.php", true, 301);
    exit();
}
?>
<?php
if (isset($_POST['submit_button']) ) {
    $courses = [];
    if (isset($_POST['course'])){
        $courses = $_POST['course'];
    }
    $array = ["session_id" =>uniqid("", true), "first_name" => $_POST['first_name'], "last_name" => $_POST['last_name'], "ufid" => $_POST['ufid'],
        "student_email" => $_POST['student_email'], "authorization" => $_POST['authorization'],
        "course" => $courses, "admin" => $_POST['admin']];
    $priv_key = get_key_for_encryption();
    $iv = get_iv_for_encryption();
    $course_list = $_POST['course'];
    if ($array["admin"] === "Admin"){
        $course_list = get_list_all_course_id();
    } elseif (is_null($course_list)){
        $course_list = [];
    }
    session_initialization($array["session_id"], $priv_key, $iv, $array["admin"], $course_list);

    insert_session([$_SESSION["session_id"],
                    encrypt_data($array["first_name"], $priv_key, $iv),
                    encrypt_data($array["last_name"], $priv_key, $iv),
                    encrypt_data($array["ufid"], $priv_key, $iv),
                    encrypt_data($array["student_email"], $priv_key, $iv),
                    encrypt_data($array["authorization"], $priv_key, $iv)
        ]);
    if ($array['authorization'] === "Student"){
        redirect_student_root();
    }
    else if ($array['authorization'] === "Faculty") {
        redirect_faculty_root();
    }


}
?>

<div class="testbox">
    <form method="POST">
        <h2 style="text-align:center; font-size: 25px;">CISE Course Equivalency Request<br>Beta Login Page</h2>
        <p style="font-size: 15px;text-align: center;">
            Welcome to the Beta of CISE Course Equivalency Request!<br>
            This page is meant to imitate the information that would be given by Shibboleth.<br>
        </p>
        <div class="item">
            <div class="name-item">
                <div>
                    <label for="first_name">First Name<span>*</span></label>
                    <input type="text" name="first_name" placeholder="James" maxlength="50" required/>
                </div>
                <div>
                    <label for="last_name">Last Name<span>*</span></label>
                    <input type="text" name="last_name" placeholder="Smith" maxlength="50" required/>
                </div>
            </div>
        </div>
        <div class="item">
            <label for="ufid">UFID (8 digit number.)<span>*</span></label>
            <input type="text" name="ufid" pattern="[0-9]{8}" required/>
        </div>

        <div class="item">
            <label for="student_email">E-mail Address (To receive a receipt of this form after you finish)
                <span>*</span></label>
            <input type="email" name="student_email" maxlength="100" required/>
        </div>
        <div class="item">
            <label for="authorization">Authorization level ("Faculty" or "Student")
                <span>*</span></label>
            <input type="text" name="authorization" maxlength="10" required/>
        </div>
        <div class="item">
            <label for="course">Faculty: courses that you are authorized to review (Ctrl + left click to click more than one, or hold click and drag.)<span>*</span></label>
            <select name="course[]" id="course[]" style="height:200px; width:100%;font-size: 18px;margin: 0 auto;display: block;top:100%;vertical-align: top;" size="6" multiple>
                <?php
                    $course_list = get_list_all_sorted_course_ids_and_courses_faculty(); // returns course ids, titles and if they are good
                    foreach ($course_list as $val) {
                        if ($val[2] != "N/A") {
                            echo "<option value ='$val[0]'>$val[0]: $val[1]</option>";
                        }
                    }
                    foreach ($course_list as $val) {
                        if ($val[2] == "N/A") {
                            echo "<option value ='$val[0]'>#$val[0]: $val[1]</option>";
                        }
                    }
                ?>

            </select>
        </div>
        <div class="item">
            <label for="admin">Admin Access (Simply write "Admin", or leave blank. This will coincide with who are given admin access based on their CID)</label>
            <input type="text" name="admin" maxlength="10"/>
        </div>
        <input class="submit" style="width:200px;background-color: #008CBA;" type="submit", name="submit_button" value="Submit">
    </form>
</div>

</body>
</html>