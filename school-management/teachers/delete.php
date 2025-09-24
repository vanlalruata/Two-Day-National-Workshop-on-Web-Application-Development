<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);
if($id){
    // find linked user
    $stmt=$conn->prepare("SELECT user_id FROM teachers WHERE teacher_id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $r=$stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($r){
        $uid=intval($r['user_id']);
        $stmt2=$conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt2->bind_param('i',$uid);
        $stmt2->execute();
        $stmt2->close();
    }
}
header("Location: list.php"); exit;
