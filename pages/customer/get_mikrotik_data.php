<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$ag_id = intval($_GET['ag_id']);

// get mikrotik id
$agent = $obj->raw_sql("SELECT mikrotik_id FROM tbl_agent WHERE ag_id = '$ag_id'");
if (empty($agent)) {
    echo json_encode(['success' => false, 'message' => 'Agent not found']);
    exit;
}

$mikrotik_id = $agent[0]['mikrotik_id'];

// get mikrotik secrets
$mikrotikData = $obj->viewAllPppSecret($mikrotik_id);
if (!$mikrotikData) {
    echo json_encode(['success' => false, 'message' => 'No Mikrotik data found']);
    exit;
}

// match agent ip/name with mikrotik secret name
$agentInfo = $obj->raw_sql("SELECT ip FROM tbl_agent WHERE ag_id = '$ag_id'");
$secretName = $agentInfo[0]['ip'] ?? '';

$secret = array_filter($mikrotikData, function ($row) use ($secretName) {
    return isset($row['name']) && $row['name'] == $secretName;
});

if (empty($secret)) {
    echo json_encode(['success' => false, 'message' => 'No secret found']);
    exit;
}

$secret = reset($secret);
$lastLogout = (!empty($secret['last-logged-out']) && $secret['last-logged-out'] != 'jan/01/1970 00:00:00')
    ? $secret['last-logged-out'] : '';

echo json_encode([
    'success' => true,
    'data' => [
        'name' => $secret['name'] ?? '',
        'password' => $secret['password'] ?? '',
        'profile' => $secret['profile'] ?? '',
        'disabled' => $secret['disabled'] ?? '',
        'last_logout' => $lastLogout,
    ]
]);