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

}elseif ($vendor == 5) {

    $oltIp     = $ip;
    $community = $community;

    /* =========================
       ECOM EPON OIDs (Tested for Diagram)
    ==========================*/
    $oidMac    = "1.3.6.1.4.1.17409.2.3.4.1.1.7";  // MAC (for index and status)
    $oidRx     = "1.3.6.1.4.1.17409.2.3.4.1.1.10"; // RX (for status)

    $linesMac = explode("\n", trim(shell_exec("snmpwalk -v2c -c $community $oltIp $oidMac 2>&1")));
    $linesRx  = explode("\n", trim(shell_exec("snmpwalk -v2c -c $community $oltIp $oidRx 2>&1")));

    $gponTree = [];

    /* =========================
       Parse MAC for ONU Indices & Base Status
    ==========================*/
    foreach ($linesMac as $line) {
        $index = null;
        $mac_raw = null;

        // Hex-STRING case
        if (preg_match('/\.(\d+)\s+=\s+Hex-STRING:\s+(.+)/', $line, $m)) {
            $index = $m[1];
            $mac_raw = trim($m[2]);
        }
        // STRING case (for non-printable MACs)
        elseif (preg_match('/\.(\d+)\s+=\s+STRING:\s+(.+)/', $line, $m)) {
            $index = $m[1];
            $mac_raw = trim($m[2], '"');
        }

        if ($index) {
            // Clean MAC
            $mac = preg_replace('/[^0-9A-Fa-f]/', '', $mac_raw);
            if (strlen($mac) == 12) {
                $mac = strtoupper(chunk_split($mac, 2, ':'));
                $mac = rtrim($mac, ':');
            } else {
                $mac = $mac_raw;  // Keep as is if not valid
            }

            // Assume PON from index (index % 8 for PON 0-7, +1 for 1-8)
            $pon = ((int)$index % 8) + 1;
            $onuName = "ONU{$pon}/{$index}";

            $gponTree[$index] = [
                'name' => $onuName,
                'pon' => $pon,
                'mac' => $mac,
                'status' => 'Offline'  // Default
            ];
        }
    }

    /* =========================
       Update Status from RX (if RX exists, Online)
    ==========================*/
    foreach ($linesRx as $line) {
        if (preg_match('/\.(\d+)\s+=\s+STRING:\s*"?(0x[0-9A-Fa-f]+|E6|CS|)"?/', $line, $m)) {
            $index = $m[1];
            $raw_rx = trim($m[2], '"');

            if (isset($gponTree[$index]) && !empty($raw_rx)) {
                $gponTree[$index]['status'] = 'Connected';  // RX data means connected
            }
        }
    }

    /* =========================
       Build Graph Data
    ==========================*/
    $links = [];
    $nodesColor = [];

    $nodesColor['OLT'] = '#000000';  // Black for OLT

    foreach ($gponTree as $index => $data) {
        $ponName = "PON{$data['pon']}";
        $onuName = $data['name'];

        // OLT -> PON
        if (!in_array(['OLT', $ponName], $links)) {
            $links[] = ['OLT', $ponName];
        }
        $nodesColor[$ponName] = '#007bff';  // Blue for PON

        // PON -> ONU
        $links[] = [$ponName, $onuName];
        $nodesColor[$onuName] = ($data['status'] == 'Connected') ? 'green' : 'red';
    }

    // Safety fallback
    if (empty($links)) {
        $links = [['OLT', 'NO_DATA']];
        $nodesColor['NO_DATA'] = 'gray';
    }

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
            foreach($nodesColor as $id => $color){
                $nodes[] = ['id' => $id, 'color' => $color];
            }
            echo json_encode($nodes);
        ?>
    }]
});
</script>

<?php
}
elseif ($vendor == 5) {

    $oltIp     = $ip;
    $community = $community;

    /* =========================
       ECOM EPON OIDs (Tested & Working)
    ==========================*/
    $oidMac    = "1.3.6.1.4.1.17409.2.3.4.1.1.7";    // MAC (প্রাইমারি)
    $oidRx     = "1.3.6.1.4.1.17409.2.3.4.1.1.10";   // RX Power (স্ট্যাটাস ডিটারমিন করার জন্য)

    function snmpLines($community, $ip, $oid){
        $cmd = "snmpwalk -v2c -c " . escapeshellarg($community) . " $ip $oid 2>&1";
        $output = shell_exec($cmd);
        return explode("\n", trim($output ?? ''));
    }

    $linesMac = snmpLines($community, $oltIp, $oidMac);
    $linesRx  = snmpLines($community, $oltIp, $oidRx);

    $gponTree = [];
    $onuCount = 0;

    /* =========================
       MAC থেকে ONU লিস্ট তৈরি
    ==========================*/
    foreach ($linesMac as $line) {
        $mac = null;
        $index = null;

        // Hex-STRING পার্স
        if (preg_match('/\.(\d+)\s+=\s+Hex-STRING:\s+(.+)/', $line, $m)) {
            $index = $m[1];
            $mac_raw = trim($m[2]);
            $mac = preg_replace('/[^0-9A-Fa-f]/', '', $mac_raw);
            $mac = strtoupper(chunk_split($mac, 2, ':'));
            $mac = rtrim($mac, ':');

            // ভ্যালিড MAC চেক (17 ক্যারেক্টার)
            if (strlen($mac) != 17 || $mac == '00:00:00:00:00:00') {
                $mac = null;
            }
        }
        // STRING পার্স (কিছু ক্ষেত্রে আসতে পারে)
        elseif (preg_match('/\.(\d+)\s+=\s+STRING:\s+(.+)/', $line, $m)) {
            $index = $m[1];
            $mac_raw = trim($m[2]);
            $mac = preg_replace('/[^0-9A-Fa-f]/', '', $mac_raw);
            if (strlen($mac) == 12) {
                $mac = strtoupper(chunk_split($mac, 2, ':'));
            } else {
                $mac = null;
            }
        }

        if ($mac && $index) {
            $pon = substr($index, -4, 1) + 1; // আনুমানিক PON (1-8)
            $ponName = "PON{$pon}";
            $onuName = "ONU{$pon}-{$index}";

            // PON গ্রুপ তৈরি
            if (!isset($gponTree[$ponName])) {
                $gponTree[$ponName] = [];
            }

            $gponTree[$ponName][] = [
                'name'   => $onuName,
                'mac'    => $mac,
                'index'  => $index,
                'status' => 'Down' // ডিফল্ট
            ];
            $onuCount++;
        }
    }

    /* =========================
       RX Power থেকে Status আপডেট
    ==========================*/
    foreach ($linesRx as $line) {
        if (preg_match('/\.(\d+)\s+=\s+STRING:\s*"?(0x[0-9A-Fa-f]+|E6|CS|)"?/', $line, $m)) {
            $index = $m[1];
            $raw = trim($m[2], '"');

            // RX আছে মানে Online
            if (!empty($raw) && $raw != '' && strpos($raw, '0x') === 0) {
                // সব PON/ONU গ্রুপে খোঁজো
                foreach ($gponTree as $ponName => &$ponData) {
                    foreach ($ponData as &$onu) {
                        if ($onu['index'] == $index) {
                            $onu['status'] = 'Connected';
                            $onu['rx'] = $raw;
                            break 2;
                        }
                    }
                }
            }
        }
    }

    /* =========================
       Build Graph Data
    ==========================*/
    $links = [];
    $nodesColor = [];
    $nodesLabel = [];

    $nodesColor['OLT'] = '#000000';
    $nodesLabel['OLT'] = 'OLT';

    foreach ($gponTree as $ponName => $ponData) {
        // OLT → PON
        $links[] = ['OLT', $ponName];
        $nodesColor[$ponName] = '#007bff'; // নীল PON
        $nodesLabel[$ponName] = $ponName;

        // PON → ONU গুলো
        $connectedCount = 0;
        foreach ($ponData as $onu) {
            $links[] = [$ponName, $onu['name']];
            
            // ONU স্ট্যাটাস অনুযায়ী কালার
            $nodesColor[$onu['name']] = 
                ($onu['status'] == 'Connected') ? 'green' : 'red';
            $nodesLabel[$onu['name']] = $onu['name'];
            
            if ($onu['status'] == 'Connected') $connectedCount++;
        }

        // PON নোডে কানেক্টেড ONU কাউন্ট যোগ
        $nodesLabel[$ponName] .= "\n({$connectedCount}/" . count($ponData) . ")";
    }

    // Safety fallback
    if (empty($links)) {
        $links = [['OLT','NO_DATA']];
        $nodesColor['NO_DATA'] = 'gray';
        $nodesLabel['NO_DATA'] = 'No ONU Found';
    }

?>

<div id="container" style="height: 600px; margin: 0 auto"></div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>

<script>
Highcharts.chart('container', {
    chart: { 
        type: 'networkgraph', 
        marginTop: 80,
        backgroundColor: 'transparent'
    },
    title: { 
        text: 'OLT → EPON → ONU Network Diagram (Dynamic SNMP)',
        style: { fontSize: '16px', fontWeight: 'bold' }
    },
    subtitle: { 
        text: 'Total ONUs: <?= $onuCount ?> | Connected: <?= array_sum(array_map(function($pon) { return count(array_filter($pon, fn($onu) => $onu['status'] == 'Connected')); }, $gponTree)) ?>',
        style: { fontSize: '12px' }
    },
    plotOptions: {
        networkgraph: {
            keys: ['from', 'to'],
            layoutAlgorithm: { 
                enableSimulation: true, 
                integration: 'verlet', 
                linkLength: 120,
                maxIterations: 100
            },
            link: {
                width: 2,
                color: {
                    linearGradient: { x1: 0, y1: 0, x2: 1, y2: 0 },
                    stops: [[0, '#e6e6e6'], [1, '#cccccc']]
                }
            },
            marker: {
                radius: 12,
                fillOpacity: 0.9,
                states: {
                    hover: { radiusPlus: 5 }
                }
            },
            dataLabels: { 
                enabled: true,
                linkFormat: '',
                allowOverlap: true,
                style: { fontSize: '10px', fontWeight: 'bold' }
            }
        }
    },
    series: [{
        marker: { 
            radius: 15,
            fillOpacity: 0.95
        },
        dataLabels: { 
            enabled: true,
            format: '{point.id}',
            style: { 
                fontSize: '11px', 
                fontWeight: 'bold',
                textOutline: '1px contrast'
            }
        },
        data: <?php echo json_encode($links); ?>,
        nodes: <?php
            $nodes = [];
            foreach($nodesColor as $id => $color) {
                $label = $nodesLabel[$id] ?? $id;
                $nodes[] = [
                    'id' => $id, 
                    'color' => $color,
                    'label' => $label,
                    'mass' => (strpos($id, 'PON') === 0) ? 5 : 3
                ];
            }
            echo json_encode($nodes);
        ?>
    }],
    tooltip: {
        pointFormat: '{point.id}: <b>{point.label}</b><br/>Status: {point.status || "N/A"}'
    },
    credits: { enabled: false }
});
</script>

<style>
#container {
    max-width: 100%;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>

<?php
}


else{
    echo "Invalid vendor";
}
