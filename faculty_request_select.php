<?php
include __DIR__ . '/include/faculty_auth_header.php';
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

function redirect_faculty_request_review($request){
    header("Location: faculty_request_review.php?request=".$request, true, 301);
    exit();
}

?>
<?php
if (isset($_POST['submit_button']) ) {
    $request = $_POST['request'];
    redirect_faculty_request_review($request);
}
?>



<div class="testbox">
        <form method="POST">
    <h2 style="text-align:center">Please select a request that you would like to review.</h2>
            <p style="text-align:center; color: darkred; font-size: 20px"><b><i><?php echo $_SESSION['info_text'] ?></i></b></p>
            <?php $_SESSION['info_text'] = "" ?>

    <fieldset>
                <legend>List of Student Requests</legend>
    <div style="margin-right: 10%;margin-left: 10%">
            <h3 style="font-size: 20px;">List of Student Requests</h3>
                <p style="font-size: 15px;">
    Below is a list of individual student requests, ordered by UF class requested and date. Students have
                    filled out a form giving the locations of each UF course topic within one of their files, which will
                    all be displayed nicely for you. You will be able to look through the files and say if the classes
                    taken outside the CISE Department are equivalent.<br>
                </p>

        <?php
        $request_rows = [];
        if ($_SESSION['admin'] == 'Admin'){
            $request_rows = get_list_all_requests_by_all_courses();
        } else {
            foreach($_SESSION['course_list'] as $course){
                $request_rows = array_merge($request_rows, get_list_all_requests_by_single_course($course));
            }
        }
        ?>

            <select name="request" id="request" style="height:200px; width:100%;font-size: 25px;margin: 0 auto;display: block;top:100%;vertical-align: top;" size="6" required>
                <?php
                foreach ($request_rows as $request){
                    echo '<option value="'.$request['RequestID'].'">';
                    echo $request['UFCourseID'].'|';
                    echo $request['TheDate'].'|';
                    echo decrypt_data($request['FirstNameCode'], $request['TheKey'], $request['iv']).'|';
                    echo decrypt_data($request['LastNameCode'], $request['TheKey'], $request['iv']).'|';
                    echo '</option>';
                }
                ?>

            </select>
            <br>
            <input class="submit" style="background-color: #008CBA;width: 200px;" type="submit", name="submit_button" value="Select">
    </div>
</fieldset>

        </form>



</div>
</body>
</html>