<?php

function sendnotification($regid, $message, $tag, $link = null)
{

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key= AAAA1I1WAU8:APA91bHVMi5wBuZOytWDyFbKs4PbDpGdW--Ljbd-lgEZuXUs2FC8a_mmpFVcV7TTtZiyyGWVRDa_b1-tXhODlWPYEMb9HLUhmi17uq9B4OfixHipETkb4XbMILz7YcGQ9gZ5uOTpOYrd';

    $token = $regid;
    $title = "InfoCards";

    $data = array('body' => $message, 'title' => $title, 'tag' => $tag, 'link' => $link);

    // $arrayToSend = array('to' => $token, 'priority' => 'high', 'notification' => $data);
    $arrayToSend = array('to' => $token, 'priority' => 'high', 'data' => $data);
    $json = json_encode($arrayToSend);
    $ch = curl_init("https://fcm.googleapis.com/fcm/send");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    $response = curl_exec($ch);
//     var_dump($response);
    curl_close($ch);
    return $response;
}
