<?php
    include __DIR__ . '/include/student_auth_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student's Course Equivalency Form</title>
    <link rel="stylesheet" href="static/css/style.css">
    <link href="<?php echo include dirname(__FILE__) . 'student_root';?>" rel="canonical">
</head>
<body>
<?php

function redirect_student_course_select(){
    header("Location: student_course_select.php", true, 301);
    exit();
}

?>
<?php
if (isset($_POST['submit_button']) ) {
    redirect_student_course_select();
}
?>
<div class="testbox">
        <form method="POST">
    <h2 style="text-align:center; font-size: 25px;">CISE Course Equivalency Request<br>Student Root Page</h2>
            <p style="text-align:center; color: darkred; font-size: 20px"><b><i><?php echo $_SESSION['info_text'] ?></i></b></p>
            <?php $_SESSION['info_text'] = "" ?>
            <br>
            <div style="margin-right: 10%;margin-left: 10%">
            <p style="font-size: 15px;">
    Course credit may be applied to CISE degree requirements for an equivalent course transferred from
                another university or taken at the University of Florida, provided that 1) the appropriate UF faculty
                approve the course as equivalent to a UF course in the entire content of the proposed course, and 2)
                UF has accepted and posted the credit on the transcript. Students requesting an equivalency review
                must follow the steps listed on the Course Equivalency Request form and present the form along with
                the required documentation to the appropriate faculty at UF - either the CISE Undergraduate
                Coordinator (for Computer Science courses) or the Undergraduate Coordinator of the department at
                UF which teaches the desired equivalent courses.</p>
                <p style="font-size: 15px;">*For transfer students admitted to any CISE undergraduate program (including CSC-Online): Course
                    equivalency requests for desired CS courses must be submitted <u><b>within the first semester at UF.</b></u>
Equivalency requests for courses that normally should be taken in first semester must be submitted as
                far in advance as possible before the term starts, and absolutely before the end of the drop/add period
                for the first term.
                </p>
                <h3 style="font-size: 20px;">Coursework from Outside UF</h3>
                <p style="font-size: 15px;">
    CISE undergraduate students can request to transfer in a <u>maximum of four courses toward required
                    core or elective Computer Science coursework</u>, dependent upon courses being deemed equivalent by
                        the Department. For evaluation of transfer courses in CISE, transfer students admitted to any of the
                        undergraduate programs offered by CISE (including CSC-Online) are required to submit Course
                        Equivalency Requests for desired CS courses by the end of their first semester at UF. See below for
    Course Equivalency request process.
                </p>
                <p style="font-size: 15px;">
                    <b>Instructions</b>: Use the correct Course Equivalency Request form. There is a specific form for
    each course held within the CISE department. Students must provide all relevant evidence and documentation of the
                    proposed course (i.e. course description, syllabus, assignments, instructor contact info.) The Course
                    Equivalency Request form and accompanying documentation must be submitted properly submitted
                    for evaluation by the appropriate faculty. Once evaluation is complete, Course Equivalency Request
                    form will be submitted to the CISE Academic Advising office.
                </p>
                <h3 style="font-size: 20px;">Coursework from Within UF</h3>
                <p style="font-size: 15px;">
    On occasion, CISE students might wish to request that a course offered or taken at UF be considered
                    as equivalent to another course required in the CISE curriculum. To request equivalency evaluation for
    such courses, students should follow the same process listed in the instructions above, except the
                    Course Equivalency Request form and accompanying documentation will be submitted to
                    Undergraduate Coordinator of the department at UF which teaches the proposed equivalent courses,
                    who will pursue evaluation by the appropriate faculty. Once evaluation is complete, Course
                    Equivalency Request form will be submitted to the CISE Academic Advising office.
                </p>

            </div>
            <input class="submit" style="background-color: #008CBA;" type="submit", name="submit_button" value="Next">
        </form>
</div>
</body>
</html>