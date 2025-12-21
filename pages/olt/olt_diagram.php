<?php
set_time_limit(0);

$id        = $_GET['id'] ?? null;
$vendor    = $_GET['vendor'] ?? null;
$ip        = $_GET['ip'] ?? null;
$community = $_GET['community'] ?? null;

if($vendor == 1){

$oltIp = $ip;
$community = $community;

$oids = [
    'descr' => "1.3.6.1.2.1.2.2.1.2",
    'oper_status' => "1.3.6.1.2.1.2.2.1.8"
];

// SNMP fetch function
function snmpBulkFetch($community, $oltIp, $oids){
    $data = [];
    foreach($oids as $key=>$oid){
        $lines = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
        foreach($lines as $line){
            if(preg_match('/\.(\d+)\s*=\s*(?:STRING|INTEGER):\s*"?(.+?)"?$/', $line, $m)){
                $index = $m[1];
                $value = $m[2];
                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

// Fetch all SNMP data
$onuData = snmpBulkFetch($community, $oltIp, $oids);

// Map SNMP oper_status to readable
$statusMap = [1=>'Connected', 2=>'Down', 3=>'Testing',4=>'Unknown',5=>'Dormant',6=>'Not Present',7=>'Lower Layer Down'];
foreach($onuData as $idx=>$onu){
    $onuData[$idx]['status'] = $statusMap[$onu['oper_status'] ?? 0] ?? 'Unknown';
}

// Build EPON tree dynamically
$eponTree = [];
foreach($onuData as $onu){
    $name = $onu['descr'] ?? '';
    $status = $onu['status'] ?? 'Unknown';

    if(preg_match('/^EPON0\/(\d+):(\d+)$/', $name, $m)){
        $port = "EPON0/".$m[1];
        $eponTree[$port]['onus'][] = ['name'=>$name, 'status'=>$status];
    } elseif(preg_match('/^EPON0\/(\d+)$/', $name)){
        $eponTree[$name]['onus'] = $eponTree[$name]['onus'] ?? [];
    }
}

// Prepare Highcharts links & node colors
$links = [];
$nodesColor = [];
foreach($eponTree as $port=>$data){
    $links[] = ['OLT', $port];
    $nodesColor[$port] = '#007bff';

    foreach($data['onus'] ?? [] as $onu){
        $links[] = [$port, $onu['name']];
        $color = match(strtolower($onu['status'])){
            'connected'=>'green',
            'down'=>'red',
            default=>'orange'
        };
        $nodesColor[$onu['name']] = $color;
    }
}
$nodesColor['OLT'] = '#000000';
?>

<div id="container" style="height: 600px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { type: 'networkgraph', marginTop: 80 },
    title: { text: 'OLT → EPON → ONU Network Diagram (Dynamic SNMP)' },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { enableSimulation: false, integration: 'verlet', linkLength: 100 }
        }
    },
    series: [{
        marker: { radius: 10 },
        dataLabels: { enabled: true },
        data: <?php echo json_encode($links); ?>,
        nodes: <?php
            $nodes = [];
            foreach($nodesColor as $id=>$color){
                $nodes[] = ['id'=>$id, 'color'=>$color];
            }
            echo json_encode($nodes);
        ?>
    }]
});
</script>

<?php

} else if ($vendor == 2){

$oltIp = $ip;
$community = $community;

$oids = [
    'descr' => "1.3.6.1.2.1.2.2.1.2",
    'oper_status' => "1.3.6.1.2.1.2.2.1.8"
];

// SNMP fetch function
function snmpBulkFetch($community, $oltIp, $oids){
    $data = [];
    foreach($oids as $key=>$oid){
        $lines = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
        foreach($lines as $line){
            if(preg_match('/\.(\d+)\s*=\s*(?:STRING|INTEGER):\s*"?(.+?)"?$/', $line, $m)){
                $index = $m[1];
                $value = $m[2];
                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

// Fetch all SNMP data
$onuData = snmpBulkFetch($community, $oltIp, $oids);

// Map SNMP oper_status to readable
$statusMap = [1=>'Connected', 2=>'Down', 3=>'Testing',4=>'Unknown',5=>'Dormant',6=>'Not Present',7=>'Lower Layer Down'];
foreach($onuData as $idx=>$onu){
    $onuData[$idx]['status'] = $statusMap[$onu['oper_status'] ?? 0] ?? 'Unknown';
}

// Build GPON tree dynamically
$gponTree = [];
foreach($onuData as $onu){
    $name = $onu['descr'] ?? '';
    $status = $onu['status'] ?? 'Unknown';

    if(preg_match('/^GPON0\/(\d+):(\d+)$/', $name, $m)){
        $port = "GPON0/".$m[1];
        $gponTree[$port]['onus'][] = ['name'=>$name, 'status'=>$status];
    } elseif(preg_match('/^GPON0\/(\d+)$/', $name)){
        $gponTree[$name]['onus'] = $gponTree[$name]['onus'] ?? [];
    }
}

// Prepare Highcharts links & node colors
$links = [];
$nodesColor = [];
foreach($gponTree as $port=>$data){
    $links[] = ['OLT', $port];
    $nodesColor[$port] = '#007bff';

    foreach($data['onus'] ?? [] as $onu){
        $links[] = [$port, $onu['name']];
        $color = match(strtolower($onu['status'])){
            'connected'=>'green',
            'down'=>'red',
            default=>'orange'
        };
        $nodesColor[$onu['name']] = $color;
    }
}
$nodesColor['OLT'] = '#000000';
?>

<div id="container" style="height: 600px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { type: 'networkgraph', marginTop: 80 },
    title: { text: 'OLT → GPON → ONU Network Diagram (Dynamic SNMP)' },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { enableSimulation: false, integration: 'verlet', linkLength: 100 }
        }
    },
    series: [{
        marker: { radius: 10 },
        dataLabels: { enabled: true },
        data: <?php echo json_encode($links); ?>,
        nodes: <?php
            $nodes = [];
            foreach($nodesColor as $id=>$color){
                $nodes[] = ['id'=>$id, 'color'=>$color];
            }
            echo json_encode($nodes);
        ?>
    }]
});
</script>

<?php

}else if($vendor == 3){

$oltIp = $ip;
$community = $community;


// === OLT credentials & OIDs ===
//$oltIp = "103.24.16.18:50502";
//$community = "bsd";
$oids = [
    'descr' => "1.3.6.1.4.1.37950.1.1.5.12.2.1.14.1.2", // Interface Name
    'oper_status' => "1.3.6.1.2.1.2.2.1.8"              // Interface Status
];

/**
 * Helper: Run SNMP Bulk Walk and return parsed data
 */
function snmpBulkFetch($community, $oltIp, $oids){
    $data = [];
    foreach($oids as $key=>$oid){
        $lines = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
        foreach($lines as $line){
            if(preg_match('/\.(\d+)\s*=\s*(?:STRING|INTEGER):\s*"?(.+?)"?$/i', $line, $m)){
                $index = $m[1];
                $value = trim($m[2]);
                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

$onuData = snmpBulkFetch($community, $oltIp, $oids);

// === Map Interface Status ===
$statusMap = [
    1 => 'Connected',
    2 => 'Down',
    3 => 'Testing',
    4 => 'Unknown',
    5 => 'Dormant',
    6 => 'Not Present',
    7 => 'Lower Layer Down'
];

foreach($onuData as $idx => $onu){
    $onuData[$idx]['status'] = $statusMap[$onu['oper_status'] ?? 0] ?? 'Unknown';
}

// === Group by EPON Port (e.g., EPON0/1) ===
$eponTree = [];

foreach($onuData as $onu){
    $name = $onu['descr'] ?? '';
    $status = $onu['status'] ?? 'Unknown';

    if (preg_match('/^(EPON\d+\/\d+):\d+$/', $name, $m)) {
        // ONU (child)
        $parent = $m[1];
        $eponTree[$parent]['onus'][] = [
            'name' => $name,
            'status' => $status
        ];
    } elseif (preg_match('/^EPON\d+\/\d+$/', $name)) {
        // PON Port (parent)
        if (!isset($eponTree[$name])) $eponTree[$name] = ['status' => $status, 'onus' => []];
    }
}

// === Sort EPON ports naturally ===
uksort($eponTree, function($a, $b){
    return strnatcmp($a, $b);
});

// === Prepare Data for Highcharts ===
$links = [];
$nodesColor = [];

foreach($eponTree as $port => $data){
    $links[] = ['OLT', $port];
    $nodesColor[$port] = '#007bff'; // Blue for ports

    // Sort ONUs naturally (EPON0/1:1, EPON0/1:2, ...)
    usort($data['onus'], fn($a,$b) => strnatcmp($a['name'], $b['name']));

    foreach($data['onus'] as $onu){
        $links[] = [$port, $onu['name']];
        $color = match(strtolower($onu['status'])){
            'connected' => 'green',
            'down' => 'red',
            default => 'orange'
        };
        $nodesColor[$onu['name']] = $color;
    }
}

$nodesColor['OLT'] = '#000000'; // Root node color
?>

<!-- === Highcharts Graph === -->
<div id="container" style="height: 650px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { type: 'networkgraph', marginTop: 80 },
    title: { text: 'OLT → EPON → ONU Network Diagram' },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { enableSimulation: false, integration: 'verlet', linkLength: 100 }
        }
    },
    series: [{
        marker: { radius: 10 },
        dataLabels: { enabled: true },
        data: <?= json_encode($links); ?>,
        nodes: <?= json_encode(array_map(fn($id,$color)=>['id'=>$id,'color'=>$color], array_keys($nodesColor), $nodesColor)); ?>
    }]
});
</script>

<?php
}else if($vendor == 4){
    $oltIp = $ip;
    $community = $community;

$oids = [
    'descr'       => "1.3.6.1.2.1.2.2.1.2",
    'oper_status' => "1.3.6.1.2.1.2.2.1.8"
];
// snmpbulkwalk -v2c -c bsd -Cr10 -t 4 -r 1 -Cc 103.178.220.124:50501

// SNMP fetch function
function snmpBulkFetch($community, $oltIp, $oids){
    $data = [];
    foreach($oids as $key=>$oid){
        $lines = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
        foreach($lines as $line){
            // match integer or string values
            if(preg_match('/\.(\d+)\s*=\s*(?:STRING|INTEGER):\s*(.*)$/i', trim($line), $m)){
                $index = $m[1];
                $value = trim($m[2], "\" ");
                // clean INTEGER prefix
                $value = preg_replace('/^INTEGER:\s*/i', '', $value);
                $value = preg_replace('/^STRING:\s*/i', '', $value);
                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

// Fetch all SNMP data
$onuData = snmpBulkFetch($community, $oltIp, $oids);

// Map SNMP oper_status to readable
$statusMap = [
    1 => 'Connected',
    2 => 'Down',
    3 => 'Testing',
    4 => 'Unknown',
    5 => 'Dormant',
    6 => 'Not Present',
    7 => 'Lower Layer Down'
];

foreach($onuData as $idx => $onu){
    $rawStatus = (int)preg_replace('/\D/', '', $onu['oper_status'] ?? '0'); // extract numeric only
    $onuData[$idx]['status'] = $statusMap[$rawStatus] ?? 'Unknown';
}

// Build EPON tree dynamically
$eponTree = [];

foreach($onuData as $onu){
    $name   = trim($onu['descr'] ?? '');
    $status = $onu['status'] ?? 'Unknown';

    // ---- 4-port VSOL (EPON0/1:1)
    if (preg_match('/^(EPON\d+\/\d+):(\d+)$/i', $name, $m)) {
        $port = $m[1];
        $eponTree[$port]['onus'][] = [
            'name'   => $name,
            'status' => $status
        ];
    }

    // ---- 4-port VSOL port only (EPON0/1)
    elseif (preg_match('/^(EPON\d+\/\d+)$/i', $name, $m)) {
        $port = $m[1];
        $eponTree[$port]['onus'] = $eponTree[$port]['onus'] ?? [];
    }

    // ---- 8-port VSOL ONU (EPON01ONU12 xxx)
    elseif (preg_match('/^(EPON\d+)ONU(\d+)/i', $name, $m)) {
        $port = $m[1];
        $eponTree[$port]['onus'][] = [
            'name'   => $name,
            'status' => $status
        ];
    }

    // ---- 8-port VSOL port only (EPON01)
    elseif (preg_match('/^(EPON\d+)$/i', $name, $m)) {
        $port = $m[1];
        $eponTree[$port]['onus'] = $eponTree[$port]['onus'] ?? [];
    }
}


// Prepare Highcharts links & node colors
$links = [];
$nodesColor = [];
foreach($eponTree as $port=>$data){
    $links[] = ['OLT', $port];
    $nodesColor[$port] = '#007bff';

    foreach($data['onus'] ?? [] as $onu){
        $links[] = [$port, $onu['name']];
        $color = match(strtolower($onu['status'])){
            'connected' => 'green',
            'down' => 'red',
            default => 'orange'
        };
        $nodesColor[$onu['name']] = $color;
    }
}
$nodesColor['OLT'] = '#000000';

// Optional debug (you can comment out later)
// echo "<pre>"; print_r($onuData); echo "</pre>";
?>

<div id="container" style="height: 600px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { type: 'networkgraph', marginTop: 80 },
    title: { text: 'OLT → EPON → ONU Network Diagram (Dynamic SNMP)' },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { enableSimulation: false, integration: 'verlet', linkLength: 100 }
        }
    },
    series: [{
        marker: { radius: 10 },
        dataLabels: { enabled: true },
        data: <?php echo json_encode($links); ?>,
        nodes: <?php
            $nodes = [];
            foreach($nodesColor as $id=>$color){
                $nodes[] = ['id'=>$id, 'color'=>$color];
            }
            echo json_encode($nodes);
        ?>
    }]
});
</script>
<?php

}elseif($vendor == 5){
$oltIp = $ip;
$community = $community;

$oids = [
    'descr' => "1.3.6.1.2.1.2.2.1.2",
    'oper_status' => "1.3.6.1.2.1.2.2.1.8"
];

// SNMP fetch function
function snmpBulkFetch($community, $oltIp, $oids){
    $data = [];
    foreach($oids as $key=>$oid){
        $lines = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
        foreach($lines as $line){
            if(preg_match('/\.(\d+)\s*=\s*(?:STRING|INTEGER):\s*"?(.+?)"?$/', $line, $m)){
                $index = $m[1];
                $value = $m[2];
                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

// Fetch all SNMP data
$onuData = snmpBulkFetch($community, $oltIp, $oids);

// Map SNMP oper_status to readable
$statusMap = [1=>'Connected', 2=>'Down', 3=>'Testing',4=>'Unknown',5=>'Dormant',6=>'Not Present',7=>'Lower Layer Down'];
foreach($onuData as $idx=>$onu){
    $onuData[$idx]['status'] = $statusMap[$onu['oper_status'] ?? 0] ?? 'Unknown';
}

// Build GPON tree dynamically
$gponTree = [];
foreach($onuData as $onu){
    $name = $onu['descr'] ?? '';
    $status = $onu['status'] ?? 'Unknown';

    // Match ONU01/02
    if (preg_match('/^ONU(\d+)\/(\d+)$/', $name, $m)) {
        $pon = "PON" . $m[1];     // PON01
        $onuName = "ONU" . $m[1] . "/" . $m[2]; // ONU01/02

        $gponTree[$pon]['onus'][] = [
            'name' => $onuName,
            'status' => $status
        ];
    }
}

// Prepare Highcharts links & node colors
$links = [];
$nodesColor = [];
foreach($gponTree as $port=>$data){
    $links[] = ['OLT', $port];
    $nodesColor[$port] = '#007bff';

    foreach($data['onus'] ?? [] as $onu){
        $links[] = [$port, $onu['name']];
        $color = match(strtolower($onu['status'])){
            'connected'=>'green',
            'down'=>'red',
            default=>'orange'
        };
        $nodesColor[$onu['name']] = $color;
    }
}
$nodesColor['OLT'] = '#000000';
?>

<div id="container" style="height: 600px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { type: 'networkgraph', marginTop: 80 },
    title: { text: 'OLT → GPON → ONU Network Diagram (Dynamic SNMP)' },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { enableSimulation: false, integration: 'verlet', linkLength: 100 }
        }
    },
    series: [{
        marker: { radius: 10 },
        dataLabels: { enabled: true },
        data: <?php echo json_encode($links); ?>,
        nodes: <?php
            $nodes = [];
            foreach($nodesColor as $id=>$color){
                $nodes[] = ['id'=>$id, 'color'=>$color];
            }
            echo json_encode($nodes);
        ?>
    }]
});
</script>

<?php
}elseif($vendor == 5){
$oltIp = $ip;
$community = $community;

$oids = [
    'descr' => "1.3.6.1.2.1.2.2.1.2",
    'oper_status' => "1.3.6.1.2.1.2.2.1.8"
];

// SNMP fetch function
function snmpBulkFetch($community, $oltIp, $oids){
    $data = [];
    foreach($oids as $key=>$oid){
        $lines = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
        foreach($lines as $line){
            if(preg_match('/\.(\d+)\s*=\s*(?:STRING|INTEGER):\s*"?(.+?)"?$/', $line, $m)){
                $index = $m[1];
                $value = $m[2];
                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

// Fetch all SNMP data
$onuData = snmpBulkFetch($community, $oltIp, $oids);

// Map SNMP oper_status to readable
$statusMap = [1=>'Connected', 2=>'Down', 3=>'Testing',4=>'Unknown',5=>'Dormant',6=>'Not Present',7=>'Lower Layer Down'];
foreach($onuData as $idx=>$onu){
    $onuData[$idx]['status'] = $statusMap[$onu['oper_status'] ?? 0] ?? 'Unknown';
}

// Build GPON tree dynamically
$gponTree = [];
foreach($onuData as $onu){
    $name = $onu['descr'] ?? '';
    $status = $onu['status'] ?? 'Unknown';

    // Match ONU01/02
    if (preg_match('/^ONU(\d+)\/(\d+)$/', $name, $m)) {
        $pon = "PON" . $m[1];     // PON01
        $onuName = "ONU" . $m[1] . "/" . $m[2]; // ONU01/02

        $gponTree[$pon]['onus'][] = [
            'name' => $onuName,
            'status' => $status
        ];
    }
}

// Prepare Highcharts links & node colors
$links = [];
$nodesColor = [];
foreach($gponTree as $port=>$data){
    $links[] = ['OLT', $port];
    $nodesColor[$port] = '#007bff';

    foreach($data['onus'] ?? [] as $onu){
        $links[] = [$port, $onu['name']];
        $color = match(strtolower($onu['status'])){
            'connected'=>'green',
            'down'=>'red',
            default=>'orange'
        };
        $nodesColor[$onu['name']] = $color;
    }
}
$nodesColor['OLT'] = '#000000';
?>

<div id="container" style="height: 600px;"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { type: 'networkgraph', marginTop: 80 },
    title: { text: 'OLT → GPON → ONU Network Diagram (Dynamic SNMP)' },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { enableSimulation: false, integration: 'verlet', linkLength: 100 }
        }
    },
    series: [{
        marker: { radius: 10 },
        dataLabels: { enabled: true },
        data: <?php echo json_encode($links); ?>,
        nodes: <?php
            $nodes = [];
            foreach($nodesColor as $id=>$color){
                $nodes[] = ['id'=>$id, 'color'=>$color];
            }
            echo json_encode($nodes);
        ?>
    }]
});
</script>

<?php
}

else{
    echo "Invalid vendor";
}
