<?php
include __DIR__ . '/include/student_auth_header.php';
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

function redirect_student_course_equiv_form($course){
    header("Location: student_course_equiv_form.php?course=".$course, true, 301);
    exit();
}

?>
<?php
if (isset($_POST['submit_button']) ) {
    $course = $_POST['course'];
    redirect_student_course_equiv_form($course);
}
?>
<div class="testbox">
    <form method="POST">



        <h2 style="text-align:center; font-size: 28px;">Instructions and Course select</h2>
        <p style="text-align:center; color: darkred; font-size: 20px"><b><i><?php $_SESSION['info_text'] ?></i></b></p>
        <fieldset>
            <legend>Instructions</legend>
            <div style="margin-right: 10%;margin-left: 10%">
                <h3 style="font-size: 20px;"><b><u>Please follow and read the following steps closely:</u></b></h3>

                <ol style="font-size: 15px;">
                    <li>If there is potentially equivalent computer science coursework from another institution, review
                        previous upper division computer science coursework (upper division=3000-4000 or 300-400
                        level courses) and compare with the upper division courses of CSE, CSC, or DAS programs.
                        Find more information in the link below.
                        <a href="https://www.cise.ufl.edu/academics/undergrad" target="_blank">https://www.cise.ufl.edu/academics/undergrad</a></li>
                    <li>Gather the required documentation for a review of up to, but no more than, four (4) previous
                        upper division computer science courses. Please make sure that <b><u>all documents are in a .pdf, .txt or image file</u></b>,
                        for ease of faculty review. To save Word Docs or PowerPoints just go to "Save As" and save file as a pdf.
                        <b>Required documentation includes:</b>
                        <ul class="instructions_for_request">
                            <li class="instructions_for_request">University catalog course description</li>
                            <li>Course syllabus</li>
                            <li>Course textbook author and title</li>
                            <li>Other materials used in the course, especially assignments</li>
                            <li>Instructor contact information (email address)</li>
                        </ul>
                    <li><u>For CS coursework:</u> UFO and campus students submit completed Course Equivalency Request form and
                        accompanying documentation on this web application for evaluation with the appropriate faculty.
                        *Note: equivalency for any CISE course is contingent upon the
                        additional and correlating equivalency for desired course. Students may not bypass or skip
                        other required courses.<br>
                        <u>For non-CS coursework:</u> Submit completed Course Equivalency Request form and
                        accompanying documentation to the Undergraduate Coordinator of the department at UF
                        which teaches the proposed equivalent courses, who will pursue evaluation by the
                        appropriate faculty.
                    </li>
                    <li>
                        After approval, signed Course Equivalency forms must be submitted to Advisors for processing.
                    </li>
                </ol>
                <br><br><br>
                <h2 style="text-align:center; font-size: 20px;">Department of Computer and Information Science and <br>
                    Engineering CISE Student Services </h2>

            </div>
        </fieldset>
        <br><br><br>


        <fieldset>
            <legend>Course Select</legend>
            <div>

                <h2 style="text-align:center">Please select the UF course to make a request for</h2>
                <p style="text-align:center; font-size: 14px">*Note: CISE undergraduate students can transfer in a <u>maximum of four courses toward required
                        core or elective Computer Science coursework</u>
                    <br>If you do not see the CISE course you wish to make a request for, please contact EMAIL HERE for it to be added to the list of courses.
                </p>

                <br>
                <select name="course" id="course" style="height:200px; width:100%;font-size: 25px;margin: 0 auto;display: block;top:100%;vertical-align: top;" size="6" required>
                        <?php
                        $course_list = get_list_all_sorted_course_ids_and_courses_student(); // returns course ids, titles and if they are good
                        foreach ($course_list as $val) {
                            echo "<option value ='$val[0]'>$val[0]: $val[1]</option>";
                        }
                        ?>

                    </select>
                </select>
                <br>
                <input class="submit" style="background-color: #008CBA;width: 175px;" type="submit" name="submit_button" value="Select Course">


            </div>
        </fieldset>
    </form>
</div>
</body>
</html>