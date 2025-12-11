<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

// Check if 'mkid' is provided in the GET request
$secretName = $_GET['name'];
$enableDisableState = $_GET['state'];
$selectedMkTik = intval($_GET['selectedMktik']);
$mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = ' . $selectedMkTik);


$mikrotikId = $mikrotikLoginData['id'];
if ($enableDisableState == 'Enable') {

    $obj->disableSingleSecret($mikrotikId, $secretName);
    echo '0 sadi' . $selectedMkTik;
} else if ($enableDisableState == 'Disable') {

    $obj->enableSingleSecret($mikrotikId, $secretName);
    echo '1';
}

//disconnect by due user menual start -- ashik
if(isset($_POST['action']) && isset($_POST['mikrotikId'])){
                $mikId = intval($_POST['mikrotikId']);
                $action = $_POST['action'];
                $singleMikrotikAgent = $obj->rawSql("SELECT 
                                                    vw_agent.*,
                                                        DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.bill_date, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS bill_date,
                                                        DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS mikrotik_disconnect,
                                                        customer_billing.dueadvance,
                                                        tbl_zone.zone_name,
                                                        _createuser.FullName 
                                                    FROM vw_agent 
                                                    left join customer_billing on customer_billing.agid = vw_agent.ag_id 
                                                    left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
                                                    left join tbl_zone on tbl_zone.zone_id = vw_agent.zone 
                                                    WHERE vw_agent.deleted_at is NULL
                                                        AND vw_agent.mikrotik_id =$mikId
                                                        AND customer_billing.dueadvance > 0 
                                                        AND vw_agent.ag_status = 1");
                        $result = false;
                
                        foreach ($singleMikrotikAgent as $agent) {
                            
                            if($action == 'disconnect'){
                                    $result = $obj->disableSingleSecret($mikId, $agent['ip']);
                                }
                                if($action == 'reconnect'){
                                    $result = $obj->enableSingleSecret($mikId, $agent['ip']);
                                }
                            $disconnectCount++;
                        }       
                        
                    
                
                // elseif($action == 'predisconnect'){
                //     $result = $obj->preDisconnectUsers($mikId);
                // }
            
                if($result){
                    echo json_encode(['success'=>true]);
                } else {
                    echo json_encode(['success'=>false, 'message'=>'Action failed']);
                }
                exit;
            }
//disconnect by due user menual end -- ashik


if (isset($_GET['mkid'])) {
    $mikrotik_id = $_GET['mkid'];
    $mikrotikConnection =  $obj->checkConnection($mikrotik_id);
    if ($mikrotikConnection) {
        $response = [
            'status' => 'Connected',
            'connection' => true,
        ];
    } else {
        $updateResult = $obj->updateData('mikrotik_user', ['status' => 0], ['id' => $mikrotik_id]);
        $response = [
            'status' => 'Failed to connect',
            'connection' => false,
        ];
    }
} elseif (isset($_GET['mkidsecretall'])) {

    $mikrotikConnection =  $obj->viewAllPppSecret($_GET['mkidsecretall']);
    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretall']]]);
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentList[$agent['ip']] = $agent['ip'];
            }
        }

        $totalEnableSecret = 0;
        $totalDisableSecret = 0;

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']] != $secret['name']) {
                continue;
            }

            if ($secret['disabled'] == 'true') {
                $totalDisableSecret++;
            } elseif ($secret['disabled'] == 'false') {
                $totalEnableSecret++;
            }


            if (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '0' && $secret['disabled'] == 'true') {
                continue;
            } elseif (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '1' && $secret['disabled'] == 'false') {
                continue;
            }

            $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
            $statusbtn = ($secret['disabled'] == 'false') ? '<button id="" data-status="1" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-success">Enable</button>' : '<button id=""  data-status="0" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-danger">Disable</button>';

            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secret['name'] . '</td>
            <td> ' . $secret['password'] . '</td>
            <td> ' . $secret['profile'] . '</td>
            <td> ' . $lastLogout . '</td>
            <td> ' . $statusbtn . '</td>
            <td>
                <form method="post" onsubmit="return confirm(&quot;Are you sure you want to delete?&quot;)">
                    <input type="hidden" name="delete_id" value="' . $secret["name"] . '">
                    <button type="submit" name="delete_static_user" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
            </tr>';
        }

        $response = [
            'status' =>  $table,
            'connection' => true,
            'totalEnableSecret' => $totalEnableSecret,
            'totalDisableSecret' => $totalDisableSecret,
            'selectedMkTik' => $selectedMkTik
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
} 
elseif (isset($_GET['mkidduedue'])) {
    $iddd = $_GET['mkidduedue'];
    $mikrotikConnection =  $obj->viewAllPppSecret($_GET['mkidduedue']);
    if ($mikrotikConnection) {
        // $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretall']]]);
       $singleMikrotikAgent = $obj->rawSql(
                                            "SELECT 
                                                vw_agent.*,
                                                DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.bill_date, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS bill_date,
                                                DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS mikrotik_disconnect,
                                                customer_billing.dueadvance,
                                                tbl_zone.zone_name,
                                                _createuser.FullName 
                                            FROM vw_agent 
                                            left join customer_billing on customer_billing.agid = vw_agent.ag_id 
                                            left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
                                            left join tbl_zone on tbl_zone.zone_id = vw_agent.zone 
                                            WHERE vw_agent.deleted_at is NULL
                                                AND vw_agent.mikrotik_id =$iddd
                                                AND customer_billing.dueadvance > 0 
                                                AND vw_agent.ag_status = 1");
        
        
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentList[$agent['ip']] = $agent['ip'];
            }
        }
        
        $totalEnableSecret = 0;
        $totalDisableSecret = 0;

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']] != $secret['name']) {
                continue;
            }

            if ($secret['disabled'] == 'true') {
                $totalDisableSecret++;
            } elseif ($secret['disabled'] == 'false') {
                $totalEnableSecret++;
            }


            if (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '0' && $secret['disabled'] == 'true') {
                continue;
            } elseif (isset($_GET['secretStatus']) && $_GET['secretStatus'] == '1' && $secret['disabled'] == 'false') {
                continue;
            }

            $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
            $statusbtn = ($secret['disabled'] == 'false') ? '<button id="" data-status="1" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-success">Enable</button>' : '<button id=""  data-status="0" data-name="' . $secret['name'] . '" class="secretCangeStatus btn btn-xs btn-danger">Disable</button>';

            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secret['name'] . '</td>
            <td> ' . $secret['profile'] . '</td>
        
            <td> ' . $lastLogout . '</td>
            <td> ' . $statusbtn . '</td>
            
            </tr>';
        }

        $response = [
            'status' =>  $table,
            'connection' => true,
            'totalEnableSecret' => $totalEnableSecret,
            'totalDisableSecret' => $totalDisableSecret,
            'selectedMkTik' => $selectedMkTik
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
}
elseif (isset($_GET['mikrotikStatikUser'])) {
    $totalInactive = 0;
    $totalActive = 0;

    $mikrotikConnection = null;
    $mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $_GET['mikrotikStatikUser']], ['status', '=', '1']]]);

    if ($mk) {
        $statikMikrotik = new Mikrotik($mk["mik_ip"], $mk["mik_port"], $mk["mik_username"], $mk["mik_password"]);

        if (!empty($statikMikrotik) && $statikMikrotik->connected) {
            // Simple Queue list fetch
            $mikrotikConnection = $statikMikrotik->comm("/queue/simple/print");
        }
    }


    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mikrotikStatikUser']]]);
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentList[$agent['ip']] = [
                    "ip" => $agent['ip'],
                    "name" => $agent['ag_name'],
                    "agent_id" => $agent['ag_id'],
                ];
            }
        }

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (!empty($secret["target"])) {
                if (@$agentList[explode("/", $secret['target'])[0]]["ip"] != explode("/", $secret['target'])[0]) {
                    continue;
                }

                if ($secret["disabled"] == "true") {
                    $totalInactive++;
                } else {
                    $totalActive++;
                }

                $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
                $statusbtn = ($secret['disabled'] == 'false') ?
                    '<button data-status="1" data-name="' . $secret['name'] . '" class="btn btn-xs btn-success changeStatus">Enable</button>' :
                    '<button  data-status="0" data-name="' . $secret['name'] . '" class="btn btn-xs btn-danger changeStatus">Disable</button>';

                $table .= '
                <tr>
                    <td>' . $sl++ . '</td>
                    <td>
                        <a class="btn btn-primary waves-effect waves-light btn-sm" href="?page=customer_ledger&token=' . $agentList[explode("/", $secret['target'])[0]]["agent_id"] . '">
                            ' . $agentList[explode("/", $secret['target'])[0]]["name"] . '
                        </a>
                    </td>
                    <td>' . $secret['target'] . '</td>
                    <td>' . $secret['name'] . '</td>
                    <td>' . $secret['rate'] . '</td>
                    <td>' . number_format((array_sum(explode("/", $secret["bytes"])) / 1024 / 1024 / 1024), 2) . " GB" . '</td>
                    <td>' . $statusbtn . '</td>
                    <td>
                        <form method="post" onsubmit="return confirm(&quot;Are you sure you want to delete?&quot;)">
                            <input type="hidden" name="delete_id" value="' . $secret[".id"] . '">
                            <button type="submit" name="delete_static_user" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>

                </tr>';
            }

            $response = [
                'status' =>  $table,
                'connection' => true,
                'totalInactive' => $totalInactive,
                'totalActive' => $totalActive,
            ];
        }
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
} elseif (isset($_GET['mkidsecretonline'])) {

    $mikrotikConnection =  $obj->pppeoActiveSecretList($_GET['mkidsecretonline']);
    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretonline']]]);
        $agentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentList[$agent['ip']] = $agent['ip'];
            }
        }

        $table = '';
        $sl = 1;
        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']] != $secret['name']) {
                continue;
            }
            $status =  (@$secret['radius'] == 'false') ? 'Running' : 'Stop';
            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secret['name'] . '</td>
            <td> ' . $secret['caller-id'] . '</td>
            <td> ' . $secret['address'] . '</td>
            <td> ' . $secret['uptime'] . '</td>
            <td>' . $status . ' </td>
            </tr>';
        }

        $response = [
            'status' =>  $table,
            'connection' => true,
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
} elseif (isset($_GET['mkidsecretunmatching'])) {

    $mikrotikConnection =  $obj->viewAllPppSecret($_GET['mkidsecretunmatching']);
    if ($mikrotikConnection) {
        $singleMikrotikAgent = $obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $_GET['mkidsecretunmatching']]]);
        $agentList = [];
        $unmatchingAgentList = [];
        if (!empty($singleMikrotikAgent)) {
            foreach ($singleMikrotikAgent as $agent) {
                $agentIP = $agent['ip'] ?? ''; // Avoid undefined index issue
                $agentList[$agentIP] = $agentIP;
            }
        }
        if (!empty($mikrotikConnection)) {
            foreach ($mikrotikConnection as $secret) {
                $unmatchingAgentList[$secret['name']] = $secret['name'];
            }
        }


        $table = '';
        $sl = 1;
        $unmatchingTable = '';
        if (!empty($singleMikrotikAgent)) {
            $i = 1;
            foreach ($singleMikrotikAgent as $agent) {
                if (@$unmatchingAgentList[$agent['ip']] == $agent['ip']) {
                    continue;
                }

                if (@$agent['deleted_at'] == NULL) {
                    $status2 =  (@$agent['ag_status'] == 1) ? 'Active' : 'Inactive';
                    $delete = '<button data-id="' . $agent['ag_id'] . '" class="btn btn-xs btn-danger secretDelete"><iconify-icon icon="mdi:delete" class="text-xl"></iconify-icon></button>';
                    $unmatchingTable .= '
                <tr>
                    <td>' . $i . '</td>
                    <td>' . $agent['ag_name'] . '</td>
                    <td>' . $agent['ip'] . '</td>
                    <td>' . $agent['queue_password'] . '</td>
                    <td>' . $agent['mb'] . '</td>
                    <td>' . $status2 . '</td>
                    <td>' . $delete . '</td>
                </tr>';
                }

                $i++;
            }
        }

        foreach ($mikrotikConnection as $secret) {
            if (@$agentList[$secret['name']] == $secret['name']) {
                continue;
            }

            $status =  (@$secret['ag_status'] == 'false') ? 'Active' : 'Inactive';
            $lastLogout = (isset($secret['last-logged-out']) && ($secret['last-logged-out'] != 'jan/01/1970 00:00:00')) ? ucfirst($secret['last-logged-out']) : '';
            $status = ($secret['disabled'] == 'false') ? 'Enable' : 'Disable';
            $actionbtn = '<button id="secretCangeStatus"  data-status="' . $secret['disabled'] . '"   data-profile="' . $secret['profile'] . '"  data-password="' . $secret['password'] . '" data-name="' . $secret['name'] . '" data-mkid="' . $_GET['mkidsecretunmatching'] . '" class="btn btn-xs btn-primary secretAddSoft"><iconify-icon icon="mdi:plus" class="text-xl"></iconify-icon></button>';
            $table .= '
            <tr>
            <td> ' . $sl++ . '</td>
            <td> ' . $secret['name'] . '</td>
            <td> ' . $secret['password'] . '</td>
            <td> ' . $secret['profile'] . '</td>
            <td> ' . $secret['service'] . '</td>
            <td> ' . $lastLogout . ' </td>
            <td> ' . $status . ' </td>
            <td> ' . $actionbtn . ' </td>
            </tr>';
        }

        $response = [
            'status' =>  $table,
            'unmatching' =>  $unmatchingTable,
            'connection' => true,
        ];
    } else {
        $response = [
            'status' =>  'No data Found',
            'connection' => false,
        ];
    }
} else if ($_POST['mkid']) {
    $mikrotik_id = $_POST['mkid'];
    $updateResult = $obj->updateData('mikrotik_user', ['status' => 1], ['id' => $mikrotik_id]);
    $response = [
        'status' => 'Data updated successfully!',
        'success' => true,
    ];
} else {
    $response = [
        'status' => 'Invalid ID provided',
        'connection' => false,
    ];
}
echo json_encode($response);
