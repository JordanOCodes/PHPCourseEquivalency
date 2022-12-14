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
function redirect_student_root($info_text)
{
    $_SESSION['info_text'] = $info_text;
    header("Location: student_root.php", true, 301);
    exit();
}
?>

<?php

if (isset($_POST['finalButton'])) {

    require __DIR__ . '/include/gather_request_data_and_clean_it.php';
    $priv_key = get_key_for_encryption();
    $iv = get_iv_for_encryption();
    $id = uniqid("", true);
    $sql_insert_list = get_student_request_info_into_list($id, $_POST, $priv_key, $iv);
    insert_student_request_row($sql_insert_list, $priv_key, $iv);
    include __DIR__ . '/include/upload_all_files.php';
    redirect_student_root("added successfully");
}
?>



<?php
$session_info = get_session_name_ufid($_SESSION['session_id']);
for ($i =0; $i < count($session_info); $i++ ){
    $session_info[$i] = decrypt_data($session_info[$i], $_SESSION['key'], $_SESSION['iv']);
}
$course_info = get_course_row($_GET['course']);
?>

<div class="testbox">
    <form id="student-form" method="POST" enctype="multipart/form-data">
        <h2 style="text-align:center; font-size: 25px;">CISE Course Equivalency Request<br><br><?php echo "$course_info[0] : $course_info[1]" ?> Request Form</h2>
        <p style="text-align:center; font-size: 14px">Please fill out the form accurately and completely.<br>
            This form is divided into two parts:<br>
            1) Giving information about the requested course(s) substitutions and proper files<br>
            2) Informing the staff in what files can <?php echo $course_info[0] ?>'s topics can be found in<br>
        </p>
        <hr/>

        <fieldset>
            <legend>Student Information</legend>
            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="first_name">First Name<span>*</span></label>
                        <input type="text" name="first_name" value="<?php echo $session_info[0] ?>" maxlength="50" readonly/>
                    </div>
                    <div>
                        <label for="last_name">Last Name<span>*</span></label>
                        <input type="text" name="last_name" value="<?php echo $session_info[1] ?>" maxlength="50" readonly/>
                    </div>
                </div>

                <div class="item">
                    <label for="ufid">UFID (8 digit number)<span>*</span></label>
                    <input type="text" name="ufid" value="<?php echo $session_info[2] ?>" readonly/>
                </div>

                <div class="item">
                    <label for="student_email">E-mail Address (To receive a receipt of this form after you finish)
                        <span>*</span></label>
                    <input type="email" name="student_email" placeholder="student@ufl.edu" maxlength="100" required/>
                </div>
            </div>
        </fieldset>
        <br>

        <fieldset>
            <legend>Course At UF</legend>
            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="course_id">Course ID: <?php echo $course_info[0] ?></label>
                        <input type="text" name="course_id" value="<?php echo $course_info[0] ?>" readonly=/>
                    </div>
                    <div>
                        <label for="course_title">Course Title: <?php echo $course_info[1] ?></label>
                        <input type="text" name="course_title" value="<?php echo $course_info[1] ?>" readonly/>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="credits">Credits: <?php echo $course_info[2] ?></label>
                        <input type="number" name="credits" value="<?php echo $course_info[2] ?>" readonly/>
                    </div>
                    <div>
                        <label for="department">Department offering course: <?php echo $course_info[3] ?></label>
                        <input type="text" name="department" value="<?php echo $course_info[3] ?>" readonly/>
                    </div>
                </div>
            </div>
            <p><b>Below is a list of topics an equivalent course to <?php echo $course_info[0] ?> would include. <br>
                    If your course(s) did not include, <i>ALL</i> of the following, please refrain from submitting an equivalency request and instead take UF CISE's <?php echo $course_info[0] ?>.</b></p>

            <ol>
                <?php
                for ($i = 4; $i < 20 + 4; $i++){ // course_list[4] starts the topics for 20 topics
                    if ($course_info[$i] != "N/A"){
                        echo "<li>$course_info[$i]</li>";
                    }
                }
                ?>
            </ol>


        </fieldset> <br>

        <div style="display: flex;flex-wrap: nowrap; flex-direction: row;  margin-bottom: 5px; justify-content: flex-start">
            <div style="">
                <input type="checkbox" class="checkbox" id="second_class" name="second_class" value="Yes" >
            </div><div style="">
                <span style="font-size: 16px; display: inline;">Check This Box If You Are Using A Second Course For This Course Request.</span><br>
            </div></div>

        <script>
            document.getElementById('second_class').onclick = function() {
                const subArrayHidden = ["first_course_title", "first_course_document_title", "second_course",
                    "second_course_document", "second_course_document_newline"];
                const subArrayRequired = ["second_sub_course_id", "second_sub_course_title", "second_sub_credits",
                    "second_sub_university", "second_sub_department", "second_sub_term", "second_sub_grade", "second_sub_email",
                    "second_sub_textbook", "second_sub_author", "second_syllabus", "second_catalog"];
                for (const element of subArrayHidden){toggleHidden(this, element);}
                for (const element of subArrayRequired){toggleRequired(this, element);}
            };
            function toggleHidden(box, id) {
                const el = document.getElementById(id);
                if ( box.checked ) {
                    el.style.display = '';
                } else {
                    el.style.display = 'none';
                    if (document.getElementById("second_is_uf_course").checked){
                        document.getElementById("second_is_uf_course").click();
                    }
                }
            }
            function toggleRequired(box, id) {
                const el = document.getElementById(id);
                if ( box.checked ) {
                    el.required = true;
                } else {
                    el.required = false;
                }
            }
        </script>

        <fieldset>
            <legend><i id="first_course_title" style="display: none">First</i> Requested Course Substitution</legend>

            <p style="text-align:left; font-size: 14px">Please provide information on the course you took that is equivalent to the CISE's <?php echo $course_info[0] ?> course.<br>
                Please note: If you took this course at UF in another department, please check the box below informing us of that.<br>
                Then please write the name of the department at UF as accurately as possible.
            </p>

            <div style="display: flex;flex-wrap: nowrap; flex-direction: row;  margin-bottom: 5px; justify-content: flex-start">
                <div style="">
                    <input type="checkbox" class="checkbox" id="is_uf_course" name="is_uf_course" value="Yes" >
                </div><div style="">
                    <span style="font-size: 16px; display: inline;">Check This Box If This Course Is/Was A Course Held At UF.</span><br>
                </div></div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_course_id">Course ID<span>*</span></label>
                        <input type="text" name="sub_course_id"  placeholder="COP3502" maxlength="20" required=/>
                    </div>
                    <div>
                        <label for="sub_course_title">Course Title<span>*</span></label>
                        <input type="text" name="sub_course_title" placeholder="Programming Fundamentals 1" maxlength="150" required/>
                    </div>
                </div>
            </div>


            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_credits">Credits<span>*</span></label>
                        <input type="number" name="sub_credits" value="3" min="0" max="9" required/>
                    </div>
                    <div id="sub_university_div">
                        <label for="sub_university">University Where Course was taken<span>*</span></label>
                        <input type="text" name="sub_university" id="sub_university" placeholder="University of Maybe Florida" maxlength="100" required/>
                    </div>
                    <div style="display: none" id="sub_department_div">
                        <label for="sub_university">Which department at The University of Florida is this course held at?<span>*</span></label>
                        <input type="text" name="sub_department" id="sub_department" placeholder="Applied Physiology and Kinesiology" value="N/A" maxlength="50"/>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_term">Term Taken<span>*</span></label>
                        <input type="text" name="sub_term" placeholder="Fall 2022" maxlength="15" required/>
                    </div>
                    <div>
                        <label for="sub_grade">Grade Received<span>*</span></label>
                        <input type="text" name="sub_grade" placeholder="3.0" maxlength="10" required/>
                    </div>
                </div>
            </div>

            <div class="item">
                <label for="sub_email">Instructor's E-mail Address<span>*</span></label>
                <input type="email" name="sub_email" maxlength="100" required/>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="sub_textbook">Primary Textbook Title<span>*</span></label>
                        <input type="text" name="sub_textbook" placeholder="This is a book title" maxlength="100" required/>
                    </div>
                    <div>
                        <label for="sub_author">Primary Textbook's Author<span>*</span></label>
                        <input type="text" name="sub_author" name="sub_author" placeholder="This is an author's name" maxlength="100" required/>
                    </div>
                </div>
            </div>

        </fieldset> <br>


        <fieldset>
            <p>To turn Microsoft Word Docs, or PowerPoint, into pdf, go to "Save As" then choose file type "PDF"</p>
            <legend><i id="first_course_document_title" style="display: none">First</i>
                Substitute Course Documents (.pdf, .txt, or image file)</legend>
            <label for="syllabus">Submit The Syllabus Here (Limit to 2MB)<span style="color: red">*</span></label>
            <input type="file" id="syllabus" name="syllabus" accept="image/*,.txt,.pdf" required><br><br>

            <label for="catalog">Submit The University Catalog Course Description Here (Limit to 2MB)<span style="color: red">*</span></label>
            <input type="file" id="catalog" name="catalog" accept="image/*,.txt,.pdf" required><br><br>

            <label for="file[]">Submit Any Other Materials Used In The Course, Especially Assignments (Limit amount of Files to 10 and under 10MB)<br>
                <i>Please & hold Ctrl (Windows) or Command (Mac) then select multiple files, if needed.</i></label>
            <input type="file" id="file[]" name="file[]" accept="image/*,.txt,.pdf" multiple><br><br>

        </fieldset><br>




        <fieldset id="second_course" style="display: none">
            <legend><i>Second</i> Requested Course Substitution</legend>
            <p style="text-align:left; font-size: 14px">Please provide information on the course you took that is equivalent to the CISE's<?php echo $course_info[0] ?> course.<br>
                Please note: If you took this course at UF in another department, please check the box below informing us of that.<br>
                Then please write the name of the department at UF as accurately as possible.
            </p>

            <div style="display: flex;flex-wrap: nowrap; flex-direction: row;  margin-bottom: 5px; justify-content: flex-start">
                <div style="">
                    <input type="checkbox" class="checkbox" id="second_is_uf_course" name="second_is_uf_course" value="Yes" >
                </div><div style="">
                    <span style="font-size: 16px; display: inline;">Check This Box If This Course Is/Was A Course Held At UF.</span><br>
                </div></div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_course_id">Course ID<span>*</span></label>
                        <input type="text" id="second_sub_course_id" name="second_sub_course_id"  placeholder="COP3502" maxlength="10"/>
                    </div>
                    <div>
                        <label for="second_sub_course_title">Course Title<span>*</span></label>
                        <input type="text" id="second_sub_course_title" name="second_sub_course_title" placeholder="Programming Fundamentals 1" maxlength="150"/>
                    </div>
                </div>
            </div>


            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_credits">Credits<span>*</span></label>
                        <input type="number" name="second_sub_credits" value="3" min="0" max="9" required/>
                    </div>
                    <div id="second_sub_university_div">
                        <label for="second_sub_university">University Where Course was taken<span>*</span></label>
                        <input type="text" name="second_sub_university" id="second_sub_university" placeholder="University of Maybe Florida" maxlength="100"/>
                    </div>
                    <div style="display: none" id="second_sub_department_div">
                        <label for="second_sub_department">Which department at The University of Florida is this course held at?<span>*</span></label>
                        <input type="text" name="second_sub_department" id="second_sub_department" placeholder="Astronomy" value="N/A" maxlength="50"/>
                    </div>
                </div>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_term">Term Taken<span>*</span></label>
                        <input type="text" id="second_sub_term" name="second_sub_term" placeholder="Fall 2022" maxlength="15"/>
                    </div>
                    <div>
                        <label for="second_sub_grade">Grade Received<span>*</span></label>
                        <input type="text" id="second_sub_grade" name="second_sub_grade" placeholder="4.0" maxlength="10"/>
                    </div>
                </div>
            </div>

            <div class="item">
                <label for="second_sub_email">Instructor's E-mail Address<span>*</span></label>
                <input type="email" id="second_sub_email" name="second_sub_email" maxlength="100"/>
            </div>

            <div class="item">
                <div class="name-item">
                    <div>
                        <label for="second_sub_textbook">Primary Textbook Title<span>*</span></label>
                        <input type="text" id="second_sub_textbook" name="second_sub_textbook" placeholder="This is a book title" maxlength="100"/>
                    </div>
                    <div>
                        <label for="second_sub_author">Primary Textbook's Author<span>*</span></label>
                        <input type="text" id="second_sub_author" name="second_sub_author" placeholder="This is an author's name" maxlength="100"/>
                    </div>
                </div>
            </div>

        </fieldset> <br>


        <fieldset id="second_course_document" style="display: none">
            <p>To turn Microsoft Word docs or PowerPoint into pdf, go to "Save As" then choose file type "PDF"</p>
            <legend><i>Second</i> Substitute Course Documents  (.pdf, .txt, or image file)</legend>
            <label for="second_syllabus">Submit The Syllabus Here (Limit to 2MB)<span style="color: red">*</span></label>
            <input type="file" id="second_syllabus" name="second_syllabus" accept="image/*,.txt,.pdf"><br><br>

            <label for="second_catalog">Submit The University Catalog Course Description Here (Limit to 2MB)<span style="color: red">*</span></label>
            <input type="file" id="second_catalog" name="second_catalog" accept="image/*,.txt,.pdf"><br><br>

            <label for="second_file[]">Submit Any Other Materials Used In The Course, Especially Assignments (Limit amount of Files to 10 and under 10MB)<br>
                <i>Please hold Ctrl (Windows) or Command (Mac) then select multiple files, if needed.</i> </label>
            <input type="file" id="second_file[]" name="second_file[]"  accept="image/*,.txt,.pdf" multiple><br><br>


        </fieldset><br id="second_course_document_newline" style="display: none">


        <script>
            document.getElementById('is_uf_course').onclick = function() {

                universityDepartmentChoice(this, "");
            };
            document.getElementById('second_is_uf_course').onclick = function() {
                universityDepartmentChoice(this, "second_");
            };
            function universityDepartmentChoice(box, prefix_str){
                const uniEl = document.getElementById(prefix_str + "sub_university");
                const deptEl = document.getElementById(prefix_str + "sub_department");
                const deptDivEl = document.getElementById(prefix_str + "sub_department_div");
                if (box.checked) {
                    uniEl.value = "University Of Florida";
                    uniEl.readOnly = true;
                    deptEl.value = "";
                    deptDivEl.style.display = '';
                }
                else {
                    uniEl.value = "";
                    uniEl.readOnly = false;
                    deptEl.value = "N/A";
                    deptDivEl.style.display = 'none';
                }
            }
        </script>


        <script>
            document.getElementById("syllabus").onchange = function() {
                checkSize(this);
            };

            document.getElementById("catalog").onchange = function() {
                checkSize(this);
            };
            document.getElementById("second_syllabus").onchange = function() {
                checkSize(this);
            };

            document.getElementById("second_catalog").onchange = function() {
                checkSize(this);
            };
            document.getElementById("file[]").onchange = function() {
                checkSize(this, false);
            };
            document.getElementById("second_file[]").onchange = function() {
                checkSize(this, false);
            };

            function checkSize(box, single=true) {
                if (single && box.files[0].size > 2097152) {
                    alert("Please Limit File To Under 2MB");
                    box.value = "";
                }
                else {
                    var amountOfFiles = box.files.length;
                    var totalSize = 0;
                    for (const theFile of box.files) {
                        totalSize = totalSize + theFile.size;
                    }
                    if (totalSize > 10485760 || amountOfFiles > 10) {
                        alert("Please Limit The Total Size of All Other Material To Be Under 10MB And Less\n And Limit The Number Of Files to 10");
                        box.value = "";
                    }
                }
            }
        </script>


        <fieldset>
            <legend>Other Comments</legend>
            <div class="item">
                <label for="other_comments">Feel Free To Leave Any Other Comments Here About Your Course Equivalency Request</label>
                <input type="text" name="other_comments" maxlength="500"/>
            </div>

        </fieldset><br>


        <div class="btn-block">
            <button type="button" onclick="openForm()">NEXT</button>
        </div>


        <div id="popupForm" class="modal">
            <div class="modal-content">
                <div class="popup-form">
                    <h2>Where Are <?php echo "$course_info[0] : $course_info[1]" ?>'s Topics Located
                    </h2>
                    <div>
                        <?php
                        for ($i = 0; $i < 20; $i++){
                            if ($course_info[$i+4] != "N/A"){
                                echo '<div class="topic_student_request" style="">';
                                echo '<p id="list_of_html_topics_'.$i.'" style="" class="list_of_html_topics_display">';
                                echo '<b>'.($i+1).'.'.$course_info[$i+4].'</b></p>';

                            }
                            else{
                                echo '<div class="topic_student_request" style="display: none">';
                                echo '<p id="list_of_html_topics_'.$i.'" style="display: none" class="list_of_html_topics_hidden">';
                                echo '<b>'.($i+1).':'.$course_info[$i+4].'</b></p>';
                            }
                            echo '<div class="topic_student_request" id="container'.$i.'"></div>';
                        echo '</div>';
                        }
                        ?>
                    </div>
                    <button type="button" onclick="checkFinalSubmit()">Submit</button><br><br>
                    <button type="button" onclick="closeForm()">Close</button>
                </div>
            </div>
        </div>

        <script>
            function openForm() {
                if (checkRequiredFields()) {
                    deleteCheckBox();
                    createCheckBox();
                    document.getElementById("popupForm").style.display = "block";
                }
            }
            function closeForm() {
                document.getElementById("popupForm").style.display = "none";
            }
            function checkRequiredFields() {
                var form = document.getElementById('student-form');
                var elements = form.elements;
                for (i=0; i<elements.length; i++){
                    if (elements[i].required){
                        if(elements[i].value === "") {
                            form.reportValidity();
                            return false;
                        }
                    }
                }
                return true;
            }


            function createCheckBox() {
                let file_checkboxes = [];
                file_checkboxes.push("syllabus", "catalog");
                for (let i = 0; i < document.getElementById("file[]").files.length; i++){
                    file_checkboxes.push("file[]" + i.toString());
                }
                if (document.getElementById('second_class').checked) {
                    file_checkboxes.push("second_syllabus", "second_catalog");
                    for (let i = 0; i < document.getElementById("second_file[]").files.length; i++){
                        file_checkboxes.push("second_file[]" + i.toString());
                    }
                }
                for (let x = 0; x < 20; x++) {
                    if (!(document.getElementById("list_of_html_topics_" + x.toString()).classList.contains("list_of_html_topics_display"))) {
                        continue
                    }
                    for (let i = 0; i < file_checkboxes.length; i++) {
                        let theFile;
                        if (file_checkboxes[i].includes("second_file[]")) {
                            let theIndex = parseInt(file_checkboxes[i].slice(13));
                            theFile = document.getElementById("second_file[]").files[theIndex]
                        }
                        else if (file_checkboxes[i].includes("file[]")) {
                            let theIndex = parseInt(file_checkboxes[i].slice(6));
                            theFile = document.getElementById("file[]").files[theIndex]
                        }
                        else {
                            theFile = document.getElementById(file_checkboxes[i]).files[0];
                        }
                        if (theFile.value !== '') {

                            var theDiv = document.createElement('div');
                            theDiv.setAttribute('class', 'checkbox_topic_student_request');
                            theDiv.setAttribute('id', 'theDiv');
                            theDiv.setAttribute('name', 'theDiv');
                            // Create checkbox (it is an input box of type checkbox).
                            var chk = document.createElement('input');

                            // Specify the type of element.
                            chk.setAttribute('type', 'checkbox');
                            chk.setAttribute('id', x.toString() + 'topic_file' + i.toString());  // Set an ID.
                            chk.setAttribute('value', 'Yes');
                            chk.setAttribute('name', x.toString() + 'topic_file' + i.toString());
                            chk.setAttribute('class', 'checkbox');

                            // Create label for checkbox.
                            var lbl = document.createElement('label' + i.toString());
                            lbl.setAttribute('for', 'topic_file' + i);

                            // Create text node and append it to the label.
                            lbl.appendChild(document.createTextNode(theFile.name));


                            var container = document.getElementById("container" + x.toString());
                            if (i === 0 &&  document.getElementById('second_class').checked) {
                                let paragraph = document.createElement('p');
                                paragraph.setAttribute('name', 'theDivParagraph');
                                const node = document.createTextNode("---------First Class---------");
                                paragraph.appendChild(node);
                                container.appendChild(paragraph);

                            }
                            if (file_checkboxes[i] === "second_syllabus" && document.getElementById('second_class').checked) {
                                let paragraph = document.createElement('p');
                                paragraph.setAttribute('name', 'theDivParagraph');
                                const node = document.createTextNode("---------Second Class---------");
                                paragraph.appendChild(node);
                                container.appendChild(paragraph);
                            }
                            // Append the newly created checkbox and label to the <p>.
                            theDiv.appendChild(chk);
                            theDiv.appendChild(lbl);

                            container.appendChild(theDiv);
                        }
                    }
                }
            }

            function deleteCheckBox() {
                let ele = document.getElementsByName("theDiv");
                for(let i=ele.length-1;i>=0;i--)
                {
                    ele[i].parentNode.removeChild(ele[i]);
                }
                let ele2 = document.getElementsByName("theDivParagraph");
                for(let j=ele2.length-1;j>=0;j--)
                {
                    ele2[j].parentNode.removeChild(ele2[j]);
                }
            }

        </script>

        <script>
            function checkFinalSubmit(){
                if (checkCheckBoxes()){
                    if (confirm("Are you sure you'd like to submit your Request")){
                        createInputsToStoreNumberOfFilesInEachClass();
                        const finalButton = document.createElement('input');
                        finalButton.setAttribute('type', 'button');
                        finalButton.setAttribute('name', 'finalButton');
                        finalButton.setAttribute('type', 'hidden');
                        const theForm = document.getElementById("student-form");
                        theForm.appendChild(finalButton);
                        finalButton.click();
                        document.getElementById("student-form").submit();

                    }
                }
            }
            function checkCheckBoxes() {
                let amountOfFiles = countAmountOfFiles();
                for (let x = 0; x < 20; x++) {
                    if (!(document.getElementById("list_of_html_topics_" + x.toString()).classList.contains("list_of_html_topics_display"))) {
                        continue
                    }
                    let checkedBoxesBooleans = [];
                    for (let i = 0; i < amountOfFiles; i++) {
                        checkedBoxesBooleans.push(document.getElementById(x.toString() + 'topic_file' + i.toString()).checked);
                    }
                    if (checkedBoxesBooleans.length > 0 && checkedBoxesBooleans.some((value) => value === true)) {
                    } else {
                        alert("Please make sure that Topic " + (x+1) + " is located in one of your files.");
                        document.getElementById("list_of_html_topics_" + x.toString()).scrollIntoView();
                        return false;
                    }
                }

                return true;
            }

            function countAmountOfFiles() {
                return countAmountOfFirstClassFiles() + countAmountOfSecondClassFiles();
            }

            function countAmountOfFirstClassFiles () {
                let amountOfFiles = 2; // "syllabus", "catalog"
                amountOfFiles += document.getElementById("file[]").files.length;
                return amountOfFiles
            }

            function countAmountOfSecondClassFiles () {
                let amountOfFiles = 0;
                if (document.getElementById('second_class').checked) {
                    amountOfFiles += 2; // "second_syllabus", "second_catalog"
                    amountOfFiles += document.getElementById("second_file[]").files.length;
                }
                return amountOfFiles;
            }

            function createInputsToStoreNumberOfFilesInEachClass() {
                const numOfFirstClassFiles = document.createElement('input');
                numOfFirstClassFiles.setAttribute('type', 'number');
                numOfFirstClassFiles.setAttribute('name', 'AmountOfFirstClassFiles');
                numOfFirstClassFiles.setAttribute('type', 'hidden');
                numOfFirstClassFiles.setAttribute('value', countAmountOfFirstClassFiles().toString());

                const numOfSecondClassFiles = document.createElement('input');
                numOfSecondClassFiles.setAttribute('type', 'number');
                numOfSecondClassFiles.setAttribute('name', 'AmountOfSecondClassFiles');
                numOfSecondClassFiles.setAttribute('type', 'hidden');
                if (document.getElementById('second_class').checked){
                    numOfSecondClassFiles.setAttribute('value', countAmountOfSecondClassFiles().toString());
                }
                else {
                    numOfSecondClassFiles.setAttribute('value', "0");
                }

                const theForm = document.getElementById("student-form");
                theForm.appendChild(numOfFirstClassFiles);
                theForm.appendChild(numOfSecondClassFiles);
            }


        </script>

        <script>
            let timer;
            let timerStart = Date.now();
            let timeSpentOnPage = 0;

            function startCounting(){
                timerStart = Date.now();
                timer = setInterval(function(){
                    timeSpentOnPage = (Date.now()-timerStart);
                    let timeSpentOnSiteSecond = parseInt(timeSpentOnPage/1000);
                    if (timeSpentOnSiteSecond > 2700){ // 2700
                        var answer = window.confirm("Your Session is about to end.\nDo you wish to continue?\nPress OK within 15 minutes to stay logged in.\nCancel to logout.");
                        if (answer){
                            timerStart = Date.now();
                            let request = new XMLHttpRequest();
                            request.onload = function() {
                                let requestStr = request.responseText;
                                if (requestStr === "False"){
                                    alert("I apologize, but your session has timed out.");
                                    <?php redirect_student_root(); ?>
                                }
                            };
                            request.open("GET", "/renew_session_lifetime", true);
                            request.send();
                        }
                        else {
                            //
                            timerStart = Date.now();
                            let request = new XMLHttpRequest();
                            request.onload = function() {
                            };
                            request.open("GET", "/clear_session_lifetime", true);
                            request.send();
                            <?php redirect_student_root(); ?>
                        }
                    }},1000);
            }
            startCounting();

        </script>

    </form>
</div>
</body>
</html>