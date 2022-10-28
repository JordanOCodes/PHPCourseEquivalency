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
function redirect_faculty_root(){
    header("Location: faculty_root.php", true, 301);
    exit();
}
?>
<?php
if (isset($_POST['ApprovedOrDenied']) ) {
    if ($_POST['ApprovedOrDenied'] == "Approved"){
        require __DIR__ . '/include/gather_request_data_and_clean_it.php';
        $priv_key = get_key_for_encryption();
        $iv = get_iv_for_encryption();
        $id = uniqid("", true);
        $sql_insert_list = get_approved_student_request_info_into_list($id, $_POST, $priv_key, $iv);
        insert_approved_student_request_row($sql_insert_list, $priv_key, $iv);

        $directory_path = "static/course_requests/".$_POST['course_id'].'/'.$_POST["request_id"];
        $file_path = "static/course_requests/".$_POST['course_id'].'/'.$_POST["request_id"].'/';
        delete_student_request_row_and_files($_POST['request_id']);
        array_map('unlink', glob("{$file_path}*"));
        rmdir($directory_path);
        $_SESSION['info_text'] = "Request Completely Approved";
        redirect_faculty_root();
    }
    elseif ($_POST['ApprovedOrDenied'] == "Denied"){
        $directory_path = "static/course_requests/".$_POST['course_id'].'/'.$_POST["request_id"];
        $file_path = "static/course_requests/".$_POST['course_id'].'/'.$_POST["request_id"].'/';
        delete_student_request_row_and_files($_POST['request_id']);
        array_map('unlink', glob("{$file_path}*"));
        rmdir($directory_path);
        $_SESSION['info_text'] = "Request Completely Denied";
        redirect_faculty_root();
    }
}
?>



<?php
$request_id = $_GET['request'];
echo  $request_id;
$request_list = get_full_request_and_course_row($request_id);
$request_list['FirstName'] =  decrypt_data($request_list['FirstName'], $request_list['TheKey'], $request_list['iv']);;
$request_list['LastName'] =  decrypt_data($request_list['LastName'], $request_list['TheKey'], $request_list['iv']);;
$request_list['email'] =  decrypt_data($request_list['email'], $request_list['TheKey'], $request_list['iv']);;
$request_list['UFID'] =  decrypt_data($request_list['UFID'], $request_list['TheKey'], $request_list['iv']);;

?>

<div class="testbox">
    <form id="request-form" method="POST">
        <p>Please review the information below.</p>
        <hr/>
        <input type="text" name="request_id" value="<?php echo $request_list['RequestID'] ?>" style="display: none" checked>
        <input type="text" name="the_date" value="<?php echo $request_list['TheDate'] ?>" style="display: none" checked>



        <fieldset>
            <legend>Student Information</legend>
            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" value="<?php echo $request_list['FirstName'] ?>"readonly/>
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" value="<?php echo $request_list['LastName'] ?>"readonly/>
                    </div>
                </div>

                <div class="item">
                    <label for="ufid">UFID</label>
                    <input type="text" name="ufid" value="<?php echo $request_list['UFID'] ?>" readonly/>
                </div>

                <div class="item">
                    <label for="student_email">E-mail Address</label>
                    <input type="email" name="email" value="<?php echo $request_list['email'] ?>" readonly/>
                </div>
            </div>
        </fieldset>
        </br>

        <fieldset>
            <legend>Course At UF</legend>
            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="course_id">Course ID</label>
                        <input type="text" name="course_id" value="<?php echo $request_list['CourseID'] ?>"readonly/>
                    </div>
                    <div>
                        <label for="course_title">Course Title</label>
                        <input type="text" name="course_title" value="<?php echo $request_list['CourseTitle'] ?>"readonly/>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="credits">Credits</label>
                        <input type="text" name="credits" value="<?php echo $request_list['Credits'] ?>"readonly/>
                    </div>
                    <div>
                        <label for="department">Department offering course</label>
                        <input type="text" name="department" value="<?php echo $request_list['Department'] ?>"readonly/>
                    </div>
                </div>
            </div>


        </fieldset> <br>



        <input type="text" name="second_class" id="second_class" value="<?php echo $request_list['IsSecondClass'] ?>" style="display: none" checked>


        <script>

            window.onload = function() {
                const subArrayHidden = ["first_course_title", "second_course", "second_course_document_newline"];
                for (const element of subArrayHidden){toggleHidden(document.getElementById("second_class"), element);}

            };
            function toggleHidden(box, id) {
                const el = document.getElementById(id);
                if ( box.value === "1" ) {
                    el.style.display = '';
                } else {
                    el.style.display = 'none';
                }
            }


        </script>


        <fieldset>
            <legend><i id="first_course_title" style="display: none">First</i> Requested Course Substitution</legend>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_course_id">Course ID<span>*</span></label>
                        <input type="text" name="sub_course_id"  value="<?php echo $request_list['FirstCourseID'] ?>" readonly=/>
                    </div>
                    <div>
                        <label for="sub_course_title">Course Title<span>*</span></label>
                        <input type="text" name="sub_course_title" value="<?php echo $request_list['FirstCourseTitle'] ?>" readonly/>
                    </div>
                </div>
            </div>


            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_credits">Credits<span>*</span></label>
                        <input type="text" name="sub_credits" value="<?php echo $request_list['FirstCredits'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="sub_university">University Where Course was taken<span>*</span></label>
                        <input type="text" name="sub_university" value="<?php echo $request_list['FirstUniversity'] ?>" readonly/>
                    </div>
                    <?php
                        if ($request_list['FirstUniversity'] === "University Of Florida"){
                            echo '<div>';
                                echo '<label for="sub_department">Department at University of Florida<span>*</span></label>';
                                echo '<input type="text" name="sub_department" value="'.$request_list['FirstDepartment'].'" readonly/>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_term">Term Taken<span>*</span></label>
                        <input type="text" name="sub_term" value="<?php echo $request_list['FirstTerm'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="sub_grade">Grade Received<span>*</span></label>
                        <input type="text" name="sub_grade" value="<?php echo $request_list['FirstGrade'] ?>" readonly/>
                    </div>
                </div>
            </div>

            <div class="item">
                <label for="sub_email">Instructor's E-mail Address<span>*</span></label>
                <input type="email" name="sub_email" value="<?php echo $request_list['FirstInstructorEmail'] ?>" readonly/>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_textbook">Primary Textbook Title<span>*</span></label>
                        <input type="text" name="sub_textbook" value="<?php echo $request_list['FirstTextbook'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="sub_author">Primary Textbook's Author<span>*</span></label>
                        <input type="text" name="sub_author" name="sub_author" value="<?php echo $request_list['FirstAuthor'] ?>" readonly/>
                    </div>
                </div>
            </div>

        </fieldset> <br>
        <br>
















        <fieldset id="second_course" style="display: none">
            <legend><i>Second</i> Requested Course Substitution</legend>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_course_id">Course ID<span>*</span></label>
                        <input type="text" id="second_sub_course_id" name="second_sub_course_id"  value="<?php echo $request_list['SecondCourseID'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="second_sub_course_title">Course Title<span>*</span></label>
                        <input type="text" id="second_sub_course_title" name="second_sub_course_title" value="<?php echo $request_list['SecondCourseTitle'] ?>" readonly/>
                    </div>
                </div>
            </div>


            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_credits">Credits<span>*</span></label>
                        <input type="text" id="second_sub_credits" name="second_sub_credits" value="<?php echo $request_list['SecondCredits'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="second_sub_university">University Where Course was taken<span>*</span></label>
                        <input type="text" id="second_sub_university" name="second_sub_university" value="<?php echo $request_list['SecondUniversity'] ?>" readonly>
                    </div>
                    <?php
                    if ($request_list['FirstUniversity'] === "University Of Florida"){
                        echo '<div>';
                        echo '<label for="sub_department">Department at University of Florida</label>';
                        echo '<input type="text" name="sub_department" value="'.$request_list['SecondDepartment'].'" readonly/>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_term">Term Taken<span>*</span></label>
                        <input type="text" id="second_sub_term" name="second_sub_term" value="<?php echo $request_list['SecondTerm'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="second_sub_grade">Grade Received<span>*</span></label>
                        <input type="text" id="second_sub_grade" name="second_sub_grade" value="<?php echo $request_list['SecondGrade'] ?>" readonly/>
                    </div>
                </div>
            </div>

            <div class="item">
                <label for="second_sub_email">Instructor's E-mail Address<span>*</span></label>
                <input type="email" id="second_sub_email" value="<?php echo $request_list['SecondInstructorEmail'] ?>" readonly/>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_textbook">Primary Textbook Title<span>*</span></label>
                        <input type="text" id="second_sub_textbook" name="second_sub_textbook" value="<?php echo $request_list['SecondTextbook'] ?>" readonly/>
                    </div>
                    <div>
                        <label for="second_sub_author">Primary Textbook's Author</label>
                        <input type="text" id="second_sub_author" name="second_sub_author" value="<?php echo $request_list['SecondAuthor'] ?>" readonly/>
                    </div>
                </div>
            </div>

        </fieldset> <br>

        <br id="second_course_document_newline" style="display: none">

        <fieldset>
            <legend>Other Comments</legend>
            <div class="item">
                <label for="other_comments">Other Comments made by the Student</label>
                <input type="text" name="other_comments" value="<?php echo $request_list['OtherComments'] ?>" readonly/>
            </div>

        </fieldset><br>








        <h2>Vertical Tabs</h2>
        <p>All the Topics appear here, you can click on them and check them off one by one until they are all crossed off</p>
        <div class="item" style="display:flex; flex-direction: column; justify-content: flex-start;">

            <?php
            $amt_first_class_files = $request_list['AmountFirstFiles'];
            $is_there_a_second_class = $request_list['IsSecondClass'];

            $file_path = "static/course_requests/".$request_list['CourseID'].'/'.$request_list["RequestID"].'/';
            $list_of_files = array_values(array_diff(scandir($file_path), array('.', '..')));

            $tab_index = 0;
            for ($i = 0; $i < count($list_of_files); $i++) {
                $tab_title = "<b>";
                if ($is_there_a_second_class == 1) {
                    if ( $i < $amt_first_class_files) {
                        $tab_title .= "First Class: ";
                    } else {
                        $tab_title .= "Second Class: ";
                    }
                }
                if ($i == 0 or $amt_first_class_files - $i == 0) {
                    $tab_title .= "Syllabus</b><br>";
                } elseif ($i == 1 or $i - $amt_first_class_files == 1) {
                    $tab_title .= "Catalog</b><br>";
                } else {
                    $tab_title .= "Extra</b><br>";
                }
                $tab_title .= mb_substr($list_of_files[$i], 2);


                if ($tab_index == 0){
                    echo '<div class="tab" style="display:flex; flex-direction: row; justify-content: flex-start;">';
                }

                    echo '<button type="button" id="tab' . $i . '" class="tablinks" ';
                    echo 'value="' . $i . '" ';
                    echo 'onclick="openFile(this.id, \'file' . $i . '\')" >';
                    echo $tab_title . '</button>';

                    if ($tab_index == 4 or $i == $amt_first_class_files - 1 or $i == count($list_of_files) - 1) {
                        echo '</div>';
                        $tab_index = 0;
                    }
                    else {
                        $tab_index += 1;
                    }
                }
            ?>


            <div class="item" style="display:flex; flex-direction: row; justify-content: flex-start;">

                <?php
                for ($i = 0; $i < count($list_of_files); $i++){
                    echo '<div id="file'.$i.'" class="tabcontent">';
                    echo '<embed src="'.$file_path.$list_of_files[$i].'"';
                    echo 'type="text/html" frameBorder="0" scrolling="auto" height="100%" width="100%">';
                    echo '</div>';
                }

                ?>

                <div style="display: flex;flex-wrap: nowrap; flex-direction: column; justify-content: flex-start;width: 30%;">
                    <?php


                    for ($i = 0; $i < 20; $i++){
                        if ($request_list['Topic'.($i+1)] != "" and $request_list['Topic'.($i+1)] != "N/A"){
                            echo '<div style="display: flex;flex-wrap: nowrap; flex-direction: row;  margin-bottom: 5px; justify-content: flex-start">';
                            echo '<div style="">';
                            echo '<input type="checkbox" class="checkbox" id="check_topic'.$i.'" value="'.$request_list['TopicLoc'.($i+1)].'" required>';
                            echo '</div>';
                            echo '<div style="">';
                            echo '<span id="check_text'.$i.'" style="display: inline;color: black;">';
                            echo $request_list['Topic'.($i+1)];
                            echo ' </span><hr>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>

            </div>


        </div>
        <script>

            function openFile(the_id, fileName) {
                let i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(fileName).style.display = "block";
                document.getElementById(the_id).className += " active";

                // Take care of highlighting each checkbox
                let checkboxes = document.getElementsByClassName("checkbox");
                let numFileLoc = parseInt(fileName.substring(4));
                for (i = 0; i < checkboxes.length; i++) {
                    let theCheckText = document.getElementById("check_text" + i.toString());
                    if (checkboxes[i].value[numFileLoc] === "1") {
                        theCheckText.classList.add('topic_found');
                        theCheckText.classList.remove('topic_missing');
                    }
                    else {
                        theCheckText.classList.add('topic_missing');
                        theCheckText.classList.remove('topic_found');
                    }

                }
            }

            // Get the element that will be  defaulted to be open and click on it
            document.getElementById("tab0").click();
        </script>

        <br>

        <fieldset>
            <legend>Comments for the student</legend>
            <div class="item">
                <label for="faculty_comments">Comments for the student about their request.</label>
                <input type="text" name="faculty_comments" placeholder="Give comments for the student here" />
            </div>

        </fieldset><br>


        <div class="btn-block">
            <button type="button" style="margin-right: 15px; background-color: red" onclick="openDenyForm()">Deny</button>
            <button type="button" style="margin-left: 15px; background-color: green;" onclick="openApproveForm()">Approve</button>
        </div>


        <div id="popupDenyForm" class="modal">
            <div class="modal-content">
                <div class="popup-form">
                    <h2>Are you sure you'd like to deny this request?
                    </h2>
                    <button type="button" style="background-color: red;" onclick="checkFinalDenySubmit()">DENY</button><br><br>
                    <button type="button" onclick="closeDenyForm()">Close</button>
                </div>
            </div>
        </div>

        <div id="popupApproveForm" class="modal">
            <div class="modal-content">
                <div class="popup-form">
                    <h2>Are you sure you'd like to Approve this request?
                    </h2>
                    <button type="button" style="background-color: green;" onclick="checkFinalApproveSubmit()">APPROVE</button><br><br>
                    <button type="button" onclick="closeApproveForm()">Close</button>
                </div>
            </div>
        </div>

        <script>
            function openDenyForm() {
                document.getElementById("popupDenyForm").style.display = "block";
            }
            function closeDenyForm() {
                document.getElementById("popupDenyForm").style.display = "none";
            }
            function openApproveForm() {
                let checkboxes = document.getElementsByClassName("checkbox");
                for (i = 0; i < checkboxes.length; i++) {
                    if (!(checkboxes[i].checked)) {
                        checkboxes[i].reportValidity();
                        return;
                    }
                }
                document.getElementById("popupApproveForm").style.display = "block";
            }
            function closeApproveForm() {
                document.getElementById("popupApproveForm").style.display = "none";
            }
        </script>

        <script>
            function checkFinalDenySubmit(){
                ApprovedOrDenied('Denied');
                document.getElementById("request-form").submit();
            }
            function checkFinalApproveSubmit(){
                ApprovedOrDenied('Approved');
                document.getElementById("request-form").submit();
            }
            function ApprovedOrDenied(str){
                const ApprovedOrDenied = document.createElement('input');
                ApprovedOrDenied.setAttribute('type', 'text');
                ApprovedOrDenied.setAttribute('name', 'ApprovedOrDenied');
                ApprovedOrDenied.setAttribute('type', 'hidden');
                ApprovedOrDenied.setAttribute('value', str);
                const theForm = document.getElementById("request-form");
                theForm.appendChild(ApprovedOrDenied);
            }
        </script>

    </form>
</div>




<script>
    var timer;
    var timerStart;
    var timeSpentOnSite = getTimeSpentOnSite();

    function getTimeSpentOnSite(){
        timeSpentOnSite = parseInt(localStorage.getItem('timeSpentOnSite'));
        timeSpentOnSite = isNaN(timeSpentOnSite) ? 0 : timeSpentOnSite;
        return timeSpentOnSite;
    }

    function startCounting(){
        timerStart = Date.now();
        timer = setInterval(function(){
            timeSpentOnSite = getTimeSpentOnSite()+(Date.now()-timerStart);
            localStorage.setItem('timeSpentOnSite',timeSpentOnSite);
            timerStart = parseInt(Date.now());
            timeSpentOnSiteSecond = parseInt(timeSpentOnSite/1000);
            if (timeSpentOnSiteSecond > 2700){
                var answer = window.confirm("Your Session is about to end.\nDo you wish to continue?\nPress OK within 15 minutes to stay logged in.\nCancel to logout.");
                if (answer){
                    localStorage.setItem('timeSpentOnSite', 0);
                    let request = new XMLHttpRequest();
                    request.onload = function() {
                        let requestStr = request.responseText;
                        if (requestStr === "False"){
                            alert("I apologize, but your session has timed out.");
                            window.location.replace("{{ url_for('student_root') }}");
                        }
                    };
                    request.open("GET", "/renew_session_lifetime", true);
                    request.send();
                }
                else {
                    //
                    localStorage.setItem('timeSpentOnSite', 0);
                    let request = new XMLHttpRequest();
                    request.onload = function() {
                    };
                    request.open("GET", "/clear_session_lifetime", true);
                    request.send();
                    window.location.replace("{{ url_for('student_root') }}");
                }
            }},1000);
    }
    startCounting();

</script>
</body>
</html>