<?php
ini_set("display_errors", "On");
include('dbconfig.php');
session_start();
$buxiban_id = $_SESSION['buxiban_id'];
$datemonth = date("Y-m");
$datetime = date("Y-m-d H:i:s");
switch ($_POST['action']) {
    //取得月份紀錄
    case "get_payment":
        $get_payment = $conn->query("select payment_time from buxiban_payment where buxiban_id = $buxiban_id")->fetchAll(PDO::FETCH_ASSOC); 
        if($get_payment){
            echo json_encode($get_payment);
        }
        else{
            echo("NO DATA");
        }
    break;

    //取得學生列表
    case "get_record_payment":
        $payment_time = $_POST['payment_time'];
        $get_record_payment = $conn->query("select record_id,record_std_name,record_parent_name,record_parent_phone,record_payment_states,record_total_price,record_payment_done from buxiban_record_payment where buxiban_id=$buxiban_id AND payment_time ='$payment_time'")->fetchAll(PDO::FETCH_ASSOC); 
        if($get_record_payment){
            echo json_encode($get_record_payment);
        }
        else{
            echo("NO DATA");
        }
    break;

    //取得選課資料
    case "get_record_selcourse":
        $record_id = $_POST['record_id'];
        $get_record_selcourse = $conn->query("select record_selcourse_name,record_selcourse_price from buxiban_record_selcourse where record_id=$record_id")->fetchAll(PDO::FETCH_ASSOC); 
        if($get_record_selcourse){
            echo json_encode($get_record_selcourse);
        }
        else{
            echo("NO DATA");
        }
    break;

    //生成該月紀錄
    case "add_new_payment":
        // 檢測該月紀錄
        $check_payment = $conn->query("select * from buxiban_payment where buxiban_id = $buxiban_id AND payment_time = '$datemonth'")->fetchAll(PDO::FETCH_ASSOC); 
        if($check_payment){
            //update
            //調record
            $get_std_selstates = $conn->query("select std_id,std_name,parent_id from buxiban_student where selcourse_states=1 AND buxiban_id=$buxiban_id")->fetch(PDO::FETCH_ASSOC);
            if($get_std_selstates){
                for($i=0;$i<count($get_std_selstates);$i++){
                    //get someone selcourse record
                    $parent_id = $get_std_selstates[$i]['parent_id'];
                    $std_name = $get_std_selstates[$i]['std_name'];
                    $std_id = $get_std_selstates[$i]['std_id'];
                    $get_record_id = $conn->query("select record_id from buxiban_record_payment where parent_id=$parent_id AND record_std_mame='$std_name'")->fetch(PDO::FETCH_ASSOC);
                    for($j=0;$j<count($get_std_selstates);$j++){
                        //del old record_selcourse
                        $record_id = $get_record_id[$j]['record_id'];
                        $del_record_selcourse = $conn->query("delete from buxiban_record_selcourse where record_id=$record_id");
                    }

                    // $get_std_selcourse = $conn->query("select course_name,course_price from buxiban_course,buxiban_selcourse where buxiban_selcourse.std_id = $std_id AND buxiban_selcourse.course_id = buxiban_course.course_id")->fetchAll(PDO::FETCH_ASSOC);
                    // if($get_std_selcourse){
                    //     for($j=0;$j<count($get_std_selcourse);$j++){
                    //         $course_name = $get_std_selcourse[$j]['course_name'];
                    //         $course_price = $get_std_selcourse[$j]['course_price'];
                    //          //add new record_selcourse
                    //         $add_new_record_selcourse = $conn->query("insert into buxiban_record_selcourse(record_id,record_selcourse_name,record_selcourse_price) value($record_id,'$course_name',$course_price) ");
                    //         $total_price += $get_std_selcourse[$j]['course_price'];
                    //     }
                    // }
                    // //update total price
                    // $update_this_record = $conn->query("update buxiban_record_payment set record_total_price=$total_price where record_id=$record_id ");


                }
                
            }

        }
        else{
            $add_new_payment = $conn->query("insert into buxiban_payment(buxiban_id,payment_time) value($buxiban_id,'$datemonth')");
            $get_std_list = $conn->query("select std_id,std_name,buxiban_parent.parent_id,parent_name,parent_phone from buxiban_student,buxiban_parent where buxiban_student.parent_id = buxiban_parent.parent_id AND buxiban_student.buxiban_id=$buxiban_id")->fetchAll(PDO::FETCH_ASSOC);
            for($i=0;$i < count($get_std_list);$i++){
                //insert data
                $std_name = $get_std_list[$i]['std_name'];
                $parent_name = $get_std_list[$i]['parent_name'];
                $parent_phone = $get_std_list[$i]['parent_phone'];
                $total_price = 0;
                $payment_time = date("Y-m");
                $parent_id = $get_std_list[$i]['parent_id'];
                //add new record_payment
                $add_new_record_payment = $conn->query("insert into buxiban_record_payment(record_std_name,record_parent_name,record_parent_phone,record_total_price,payment_time,parent_id,buxiban_id) value('$std_name','$parent_name','$parent_phone',$total_price,'$payment_time',$parent_id,$buxiban_id)");
                //search selcourse
                $get_this_record_id = $conn->query("select record_id from buxiban_record_payment where parent_id =$parent_id AND payment_time='$payment_time' AND record_std_name='$std_name'")->fetch(PDO::FETCH_ASSOC);
                $record_id = $get_this_record_id['record_id'];
                $std_id = $get_std_list[$i]['std_id'];

                $get_std_selcourse = $conn->query("select course_name,course_price from buxiban_course,buxiban_selcourse where buxiban_selcourse.std_id = $std_id AND buxiban_selcourse.course_id = buxiban_course.course_id")->fetchAll(PDO::FETCH_ASSOC);
                if($get_std_selcourse){
                    for($j=0;$j<count($get_std_selcourse);$j++){
                        $course_name = $get_std_selcourse[$j]['course_name'];
                        $course_price = $get_std_selcourse[$j]['course_price'];
                         //add new record_selcourse
                        $add_new_record_selcourse = $conn->query("insert into buxiban_record_selcourse(record_id,record_selcourse_name,record_selcourse_price) value($record_id,'$course_name',$course_price) ");
                        $total_price += $get_std_selcourse[$j]['course_price'];
                    }
                }
                //update total price
                $update_this_record = $conn->query("update buxiban_record_payment set record_total_price=$total_price where record_id=$record_id ");
            }
        }
    break;

    //切換繳款狀態
    case "update_payment_states":
        $record_id = $_POST['record_id'];
        $record_payment_states = $_POST['record_payment_states'];
        if($record_payment_states==0){
            $sqlsend = $conn->query("update buxiban_record_payment set record_payment_states=NOT record_payment_states,record_payment_done='$datetime' where record_id = $record_id"); 
        }
        else{
            $sqlsend = $conn->query("update buxiban_record_payment set record_payment_states=NOT record_payment_states,record_payment_done='0000-00-00 00:00:00' where record_id = $record_id"); 
        }
        break;
}
$conn = null;
?>