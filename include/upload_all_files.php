<?php
if (!file_exists('static/course_requests/'.$_POST['course_id'])) {
    mkdir('static/course_requests/'.$_POST['course_id'], 0777, true);
}
$dir_to_file = 'static/course_requests/'.$_POST['course_id'].'/'.$id.'/';
mkdir($dir_to_file, 0777, true);

$file_count = -1;
for ($i = 0; $i < 2; $i++) {
    $pre = "";
    if ($i === 1) {
        if (isset($_POST['second_class'])){
            $pre = "second_";
        } else { continue;}}
    $file_count++;
    file_upload_syllabus_or_catalog($file_count, $pre ,"syllabus", $dir_to_file);

    $file_count++;
    file_upload_syllabus_or_catalog($file_count, $pre,  "catalog", $dir_to_file);
    if(!(empty($_FILES[$pre.'file']['tmp_name'][0]))) {
        $file_array_count = count($_FILES[$pre.'file']['tmp_name']);
        for ($j = 0; $j < $file_array_count; $j++) {
            $file_count++;
            $file_temp_name = $_FILES[$pre.'file']['tmp_name'][$j];
            $error = $_FILES[$pre.'file']['error'][$j];
            $file_ext = explode('.', $_FILES[$pre.'file']['name'][$j]);
            $actual_file_ext = strtolower(end($file_ext));
            file_upload($dir_to_file, $file_temp_name, $file_count, "Extra" . ($j + 1), $actual_file_ext, $error);
        }
    }
}

function file_number_prefix($number): string
{
    if ($number < 10){
        return "0".$number;
    }
    else{
        return strval($number);
    }
}

function file_upload_syllabus_or_catalog($file_count, $pre, $name_str, $new_location){
    if(isset($_FILES[$pre.$name_str])) {
        $file = $_FILES[$pre.$name_str];
        $temp_location = $file['tmp_name'];
        $error = $file['error'];
        $file_ext = explode('.', $file['name']);
        $actual_file_ext = strtolower(end($file_ext));
        file_upload($new_location, $temp_location, $file_count, $name_str, $actual_file_ext, $error);
    }
}

function file_upload($new_location, $temp_location, $file_count, $file_name, $file_ext, $error){
    $allowed_ext = array('jpg','jpeg','img','png','txt','pdf');
    if (in_array($file_ext, $allowed_ext)){
        if ($error === 0){
            $actual_file_name = file_number_prefix($file_count).$file_name.'.'.$file_ext;
            move_uploaded_file($temp_location, $new_location.$actual_file_name);
        } else {
            echo "There was an error uploading your file!";
        }
    } else{
        echo "That is not an allowed file type";
    }
}
?>