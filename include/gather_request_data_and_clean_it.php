<?php

function get_student_request_info_into_list($id, $post, $priv_key, $iv): array
{
    $sql_list = array();
    array_push($sql_list, $id,
        encrypt_data($post['first_name'], $priv_key, $iv),
        encrypt_data($post['last_name'], $priv_key, $iv),
        encrypt_data($post['ufid'], $priv_key, $iv),
        encrypt_data($post['student_email'], $priv_key, $iv),
        $post['course_id'],
        $post['sub_course_id'], $post['sub_course_title'], $post['sub_credits'], $post['sub_university'],
        $post['sub_department'], $post['sub_term'], $post['sub_grade'], $post['sub_email'],
        $post['sub_textbook'], $post['sub_author']
    );
    if (isset($post['second_class'])){ // That means we have a second class!
        array_push($sql_list, "1",
            $post['second_sub_course_id'], $post['second_sub_course_title'], $post['second_sub_credits'],
            $post['second_sub_university'], $post['second_sub_department'], $post['second_sub_term'],
            $post['second_sub_grade'], $post['second_sub_email'], $post['second_sub_textbook'],
            $post['second_sub_author']

        );
    }
    else {
        array_push($sql_list, "0",
            "N/A", "N/A",
            "0", "N/A",
            "N/A", "N/A",
            "N/A", "N/A",
            "N/A", "N/A"

        );
    }
    for ($i = 0; $i < 20; $i++){
        $topic_str = "";
        for ($j = 0; $j < 24; $j++){
            if (isset($post[$i.'topic_file'.$j])){
                $topic_str .= '1';
            }
            else {
                $topic_str .= 0;
            }
        }
        $sql_list[] = $topic_str;
    }
    array_push($sql_list, $post['AmountOfFirstClassFiles'], $post['AmountOfSecondClassFiles'],
        $post['other_comments']);

    return $sql_list;
}


function get_approved_student_request_info_into_list($id, $post, $priv_key, $iv): array
{
    $sql_list = array();
    array_push($sql_list, $id,
        encrypt_data($post['first_name'], $priv_key, $iv),
        encrypt_data($post['last_name'], $priv_key, $iv),
        encrypt_data($post['ufid'], $priv_key, $iv),
        encrypt_data($post['student_email'], $priv_key, $iv),
        $post['course_id'], $post['course_title'], $post['credits'], $post['department'],
        $post['sub_course_id'], $post['sub_course_title'], $post['sub_credits'], $post['sub_university'],
        $post['sub_department'], $post['sub_term'], $post['sub_grade'],
    );
    if ($post['second_class'] == 1){ // That means we have a second class!
        array_push($sql_list, "1",
            $post['second_sub_course_id'], $post['second_sub_course_title'], $post['second_sub_credits'],
            $post['second_sub_university'], $post['second_sub_department'], $post['second_sub_term'],
            $post['second_sub_grade']

        );
    }
    else {
        array_push($sql_list, "0",
            "N/A", "N/A",
            "0", "N/A",
            "N/A", "N/A",
            "N/A"
        );
    }

    array_push($sql_list, $post['faculty_comments']);

    return $sql_list;
}

?>
