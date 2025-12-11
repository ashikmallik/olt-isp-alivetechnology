<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

if (!isset($_GET['name']) || !isset($_GET['selectedMktik']) || !isset($_GET['state'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$secretName = $_GET['name'];
$selectedMkTik = intval($_GET['selectedMktik']);
$enableDisableState = $_GET['state'];
if ($enableDisableState == 'Disable') {
    // ENABLE user
    $obj->enableSingleSecret($selectedMkTik, $secretName);
    echo json_encode(['success' => true, 'message' => 'User enabled successfully']);
} elseif ($enableDisableState == 'Enable') {
    // DISABLE user
    $obj->disableSingleSecret($selectedMkTik, $secretName);
    echo json_encode(['success' => true, 'message' => 'User disabled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid state value']);
}
