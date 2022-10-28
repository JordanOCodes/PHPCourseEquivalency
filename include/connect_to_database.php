<?php

$request_attributes = ["RequestID", "TheDate",
    "FirstName", "LastName", "UFID", "email",
    "UFCourseID",
    "FirstCourseID", "FirstCourseTitle", "FirstCredits", "FirstUniversity", "FirstDepartment",
    "FirstTerm", "FirstGrade", "FirstInstructorEmail", "FirstTextbook", "FirstAuthor",
    "IsSecondClass",
    "SecondCourseID", "SecondCourseTitle", "SecondCredits", "SecondUniversity", "SecondDepartment",
    "SecondTerm", "SecondGrade", "SecondInstructorEmail", "SecondTextbook", "SecondAuthor",
    "TopicLoc1", "TopicLoc2", "TopicLoc3", "TopicLoc4", "TopicLoc5", "TopicLoc6", "TopicLoc7",
    "TopicLoc8", "TopicLoc9", "TopicLoc10",
    "TopicLoc11", "TopicLoc12", "TopicLoc13", "TopicLoc14", "TopicLoc15", "TopicLoc16", "TopicLoc17",
    "TopicLoc18", "TopicLoc19", "TopicLoc20",
    "AmountFirstFiles", "AmountSecondFiles", "OtherComments"
];

$course_attributes = [
    "CourseID", "CourseTitle", "Credits", "Department",
    "Topic1", "Topic2", "Topic3", "Topic4", "Topic5", "Topic6", "Topic7", "Topic8", "Topic9", "Topic10",
    "Topic11", "Topic12", "Topic13", "Topic14", "Topic15", "Topic16", "Topic17", "Topic18", "Topic19", "Topic20"
];



function connect_to_database(){
    $servername = '127.0.0.1';
    $database = 'courseequivalency';
    $username = "root";
    $password = "root";


// Create connection
    $conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function get_list_all_course_id() {
    /*
     * param:
     * return: Returns an array of strings of UF CourseIDs
     */
    $conn = connect_to_database();
    $query = "SELECT CourseID FROM Course";
    $rows = [];

    if ($stmt = $conn->prepare($query)) {


        if ($result = $conn -> query($query)) {
            while ($row = $result->fetch_row()) {
                $rows[] = $row[0];
            }
            $result->free_result();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();

    return $rows;
}

/*
 * @return:
 */
function get_list_all_sorted_course_ids_and_courses_faculty(): array
{
    /*
     * param:
     * return: A list of [[courseID0, courseTitle0, WordsAboutIFFinished0], [courseID1, courseTitle1, Words1]...]
     */
    $conn = connect_to_database();
    $query = "SELECT CourseID, CourseTitle, Topic1 
              FROM Course
              ORDER BY CourseID ASC";
    $rows = [];

    if ($stmt = $conn->prepare($query)) {


        if ($result = $conn -> query($query)) {
            while ($row = $result->fetch_row()) {
                $rows[] = [$row[0], $row[1], $row[2]];
            }
            $result->free_result();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();

    return $rows;
}

/*
 * param:
 * return: A list of [[courseID0, courseTitle0, WordsAboutIFFinished0], [courseID1, courseTitle1, Words1]...]
 */
function get_list_all_sorted_course_ids_and_courses_student(): array
{
    $conn = connect_to_database();
    $query = "SELECT CourseID, CourseTitle
              FROM Course
              WHERE Topic1 != 'N/A'
              ORDER BY CourseID ASC";
    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();

        if ($result = $stmt ->get_result()) {

            while ($row = $result->fetch_row()) {
                $rows[] = [$row[0], $row[1]];
            }
            $result->free_result();
        }
        var_dump($rows);
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();

    return $rows;
}


function get_course_row($course_id): array
{
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "SELECT * FROM Course WHERE CourseID = ?";

    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s',$course_id);
        $stmt->execute();

        if ($result = $stmt ->get_result()) {
            while ($row = $result->fetch_row()) {
                $rows[] = $row;
            }
            $result->free_result();
        }
        $stmt->close();
    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();
    return $rows[0];
}


/*
 * param:
 * return:
 */
function insert_student_request_row($array, $priv_key, $iv) {
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "INSERT INTO StudentRequest (RequestID,
        FirstNameCode, LastNameCode, UFIDCode, emailCode,
        UFCourseID,
        FirstCourseID, FirstCourseTitle, FirstCredits, FirstUniversity, FirstDepartment,
        FirstTerm, FirstGrade, FirstInstructorEmail, FirstTextbook, FirstAuthor,
        IsSecondClass,
        SecondCourseID, SecondCourseTitle, SecondCredits, SecondUniversity, SecondDepartment, SecondTerm, SecondGrade,
        SecondInstructorEmail, SecondTextbook, SecondAuthor,
        TopicLoc1, TopicLoc2,TopicLoc3, TopicLoc4, TopicLoc5, TopicLoc6, TopicLoc7, TopicLoc8,TopicLoc9, TopicLoc10,
        TopicLoc11, TopicLoc12,TopicLoc13,TopicLoc14, TopicLoc15, TopicLoc16,TopicLoc17, TopicLoc18, TopicLoc19, TopicLoc20,
        AmountFirstFiles, AmountSecondFiles, OtherComments)
        VALUES (?,
        ?,?,?,?,
        ?,
        ?,?,?,?,?,
        ?,?,?,?,?,
        ?,
        ?,?,?,?,?,?,?,
        ?,?,?,
        ?,?,?,?,?,?,?,?,?,?,
        ?,?,?,?,?,?,?,?,?,?,
        ?,?,?)
         ";
    $query_key = "INSERT INTO RequestKey (RequestKeyID, TheKey, iv)
        VALUES (?,?,?)";

    if ($stmt = $conn->prepare($query)) {
        $types = str_repeat('s', count($array));
        $stmt->bind_param($types, ...$array);
        $stmt->execute();
        $stmt->close();

    } else {
        echo $conn->error;
    }
    if ($stmt_key = $conn->prepare($query_key)) {
        $stmt_key->bind_param("sss", $array[0], $priv_key, $iv);
        $stmt_key->execute();
        $stmt_key->close();
    }
    else {
        echo $conn->error;
    }
    if (!$conn->commit()) {
        echo $conn->error;
        exit();
    }
    $conn->close();

    return;
}




function delete_student_request_row_and_files($request_id) {
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "DELETE FROM StudentRequest
              WHERE RequestID = ?
         ";
    $query_key = "DELETE FROM RequestKey
                  WHERE RequestKeyID = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s',$request_id);
        $stmt->execute();
        $stmt->close();

    } else {
        echo $conn->error;
    }
    if ($stmt_key = $conn->prepare($query_key)) {
        $stmt_key->bind_param('s',$request_id);
        $stmt_key->execute();
        $stmt_key->close();
    }
    else {
        echo $conn->error;
    }
    if (!$conn->commit()) {
        echo $conn->error;
        exit();
    }
    $conn->close();

    return;
}




/*
 * param:
 * return:
 */
function insert_approved_student_request_row($array, $priv_key, $iv) {
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "INSERT INTO ApprovedRequest (ID,
        FirstNameCode, LastNameCode, HashedUFID, emailCode,
        UFCourseID, UFCourseTitle, UFCredits, UFDepartment,
        FirstCourseID, FirstCourseTitle, FirstCredits, FirstUniversity, FirstDepartment, FirstTerm, FirstGrade,
        IsSecondClass,
        SecondCourseID, SecondCourseTitle, SecondCredits, SecondUniversity, SecondDepartment, SecondTerm, SecondGrade,
        FacultyComments)
        VALUES (?,
        ?,?,?,?,
        ?,
        ?,?,?,?,?,
        ?,?,?,?,?,
        ?,
        ?,?,?,?,?,?,?,
        ?
        )
         ";
    $query_key = "INSERT INTO ApprovedKey (KeyID, TheKey, iv)
        VALUES (?,?,?)";

    if ($stmt = $conn->prepare($query)) {
        $types = str_repeat('s', count($array));
        $stmt->bind_param($types, ...$array);
        $stmt->execute();
        $stmt->close();

    } else {
        echo $conn->error;
    }
    if ($stmt_key = $conn->prepare($query_key)) {
        $stmt_key->bind_param("sss", $array[0], $priv_key, $iv);
        $stmt_key->execute();
        $stmt_key->close();
    }
    else {
        echo $conn->error;
    }
    if (!$conn->commit()) {
        echo $conn->error;
        exit();
    }
    $conn->close();

    return;
}







function get_list_all_requests_by_all_courses(): array
{
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "SELECT StudentRequest.RequestID, UFCourseID, TheDate, FirstNameCode, LastNameCode, TheKey, iv 
              FROM StudentRequest 
              JOIN RequestKey ON StudentRequest.RequestID = RequestKey.RequestKeyID
              ORDER BY TheDate DESC";

    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();

        if ($result = $stmt ->get_result()) {
            while ($row = $result->fetch_row()) {
                $real_row = array("RequestID" => $row[0], "UFCourseID" => $row[1], "TheDate" => $row[2],
                    "FirstNameCode" => $row[3], "LastNameCode" => $row[4], "TheKey" => $row[5], "iv" => $row[6]
                );
                $rows[] = $real_row;
            }
            $result->free_result();
        }
        $stmt->close();
    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();
    return $rows;
}


function get_list_all_requests_by_single_course($course_id): array
{
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "SELECT StudentRequest.RequestID, UFCourseID, TheDate, FirstNameCode, LastNameCode, TheKey, iv 
              FROM StudentRequest 
              JOIN RequestKey ON StudentRequest.RequestID = RequestKey.RequestKeyID
              WHERE UFCourseID = ?
              ORDER BY TheDate DESC";

    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s',$course_id);
        $stmt->execute();

        if ($result = $stmt ->get_result()) {
            while ($row = $result->fetch_row()) {
                $real_row[] = array("RequestID" => $row[0], "UFCourseID" => $row[1], "TheDate" => $row[2],
                                "FirstNameCode" => $row[3], "LastNameCode" => $row[4], "TheKey" => $row[5], "iv" =>$row[6]
                                );
                $rows = $real_row;
            }
            $result->free_result();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();
    return $rows;
}


function get_full_request_and_course_row($request_id): array
{
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "SELECT RequestKey.TheKey, RequestKey.iv, StudentRequest.*, Course.*  FROM StudentRequest
                              JOIN RequestKey ON StudentRequest.RequestID = RequestKey.RequestKeyID
                              JOIN Course ON StudentRequest.UFCourseID = Course.CourseID
                              WHERE RequestID = ?";

    $rows = array();

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s',$request_id);
        $stmt->execute();

        if ($result = $stmt ->get_result()) {
            while ($row = $result->fetch_row()) {
                $real_row = array("TheKey" => $row[0], "iv" => $row[1]);
                $row_index = 2;
                global  $request_attributes;
                foreach ($request_attributes as $attribute){
                    $real_row = array_merge($real_row, array($attribute => $row[$row_index]));
                    $row_index++;
                }
                global  $course_attributes;
                foreach ($course_attributes as $attribute){
                    $real_row = array_merge($real_row, array($attribute => $row[$row_index]));
                    $row_index++;
                }

                $rows = $real_row;
            }
            $result->free_result();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }
    $conn->close();
    return $rows;
}


/*
 * param:
 * return:
 */
function insert_session($array) {
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "INSERT INTO Session (SessionID, FirstNameCode, LastNameCode, UFIDCode,emailCode, AuthCode)
              VALUES (?,?,?,?,?,?)";

    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $types = str_repeat('s', count($array));
        $stmt->bind_param($types,$array[0],$array[1],$array[2],$array[3],$array[4],$array[5]);
        $stmt->execute();
        if (!$conn -> commit()) {
            echo "Commit transaction failed";
            exit();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }

    $conn->close();

    return $rows;
}


function get_session_auth_code($session_id) : string {
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "SELECT AuthCode FROM Session WHERE SessionID = ?";

    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s',$session_id);
        $stmt->execute();

        if ($result = $stmt ->get_result()) {
            while ($row = $result->fetch_row()) {
                $rows[] = $row[0];
            }
            $result->free_result();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }

    $conn->close();

    return $rows[0];
}


function get_session_name_ufid($session_id): array
{
    $conn = connect_to_database();
    $conn -> autocommit(FALSE);
    $query = "SELECT FirstNameCode, LastNameCode, UFIDCode FROM Session WHERE SessionID = ?";

    $rows = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s',$session_id);
        $stmt->execute();

        if ($result = $stmt ->get_result()) {
            while ($row = $result->fetch_row()) {
                $rows[] = $row;
            }
            $result->free_result();
        }
        $stmt->close();

    } else {
        echo "failed to fetch data\n";
    }

    $conn->close();

    return $rows[0];
}

//
//if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
//    //echo "HIIII\n";
//    //$rows = get_request_and_course_row("635a9eae988237.23833525");
//    //var_dump($rows);
//    $str = "01234567";
//    echo mb_substr($str, 2);
//
//}
?>