<?php
require_once __DIR__ . '/../inc/header.php';
require_role('admin');

$id=intval($_GET['id']??0);
if($id){
    $stmt=$conn->prepare("DELETE FROM subjects WHERE subject_id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->close();
}
header("Location: list.php"); exit;
