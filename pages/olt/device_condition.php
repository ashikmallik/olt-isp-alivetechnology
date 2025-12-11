<?php
set_time_limit(300);

$id        = $_GET['id'] ?? null;
$vendor    = $_GET['vendor'] ?? null;
$ip        = $_GET['ip'] ?? null;
$community = $_GET['community'] ?? null;

if($vendor == 1){

$oltIp = $ip;
$community = $community;

$oids = [
    'name'     => "1.3.6.1.2.1.2.2.1.2",
    'rx_power' => "1.3.6.1.4.1.3320.101.10.5.1.5",
    'tx_power' => "1.3.6.1.4.1.3320.101.10.5.1.6",
    'distance' => "1.3.6.1.4.1.3320.101.10.1.1.27",
    'serial'   => "1.3.6.1.4.1.3320.101.10.1.1.3",
    'download' => "1.3.6.1.2.1.31.1.1.1.10", 
    'upload'   => "1.3.6.1.2.1.31.1.1.1.6", 
];

function snmpBulkWalk($community, $oltIp, $oids) {
    $data = [];
    foreach ($oids as $key => $oid) {
        $cmd = "snmpbulkwalk -v2c -c bsd -Cr400 -t 1 -r 1 $oltIp $oid 2>&1";
        $output = shell_exec($cmd);
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            if (empty($line)) continue;

            if (preg_match('/\.(\d+) = (?:STRING|INTEGER|Hex-STRING|Gauge32|Counter64): ?"?(.+?)"?$/', $line, $matches)) {
                $index = $matches[1];
                $value = $matches[2];

                if (in_array($key, ['rx_power', 'tx_power'])) {
                    $value = (int)$value;
                    if ($value == -65535) continue;
                    $value = $value / 10;
                }

                if ($key === 'serial') {
                    $hex = preg_replace('/[^0-9A-Fa-f ]/', '', $value);
                    $value = strtoupper(str_replace(' ', ':', trim($hex)));
                }

                if (in_array($key, ['download','upload'])) {
                    $value = (int)$value;
                }

                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

$onuData = snmpBulkWalk($community, $oltIp, $oids);

$onuPorts = array_filter($onuData, function ($item) {
    return isset($item['name']) && preg_match('/^EPON\d+\/\d+:\d+$/', $item['name']);
});

uasort($onuPorts, function ($a, $b) {
    preg_match('/EPON(\d+)\/(\d+):(\d+)/', $a['name'], $m1);
    preg_match('/EPON(\d+)\/(\d+):(\d+)/', $b['name'], $m2);
    return [(int)$m1[1], (int)$m1[2], (int)$m1[3]] <=> [(int)$m2[1], (int)$m2[2], (int)$m2[3]];
});
?>

<!-- HTML Output -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-info text-dark me-3">Total ONUs: <?= count($onuPorts) ?></span>
        <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th scope="col">SL</th>
                    <th scope="col">Interface</th>
                    <th scope="col">Distance</th>
                    <th scope="col">Tx Power (dBm)</th>
                    <th scope="col">Rx Power (dBm)</th>
                    <th scope="col">Download (GB)</th>
                    <th scope="col">Upload (GB)</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php $sl = 1; ?>
                <?php foreach ($onuPorts as $onu): ?>
                    <tr>
                        <td><?= $sl++ ?></td>
                        <td><?= htmlspecialchars($onu['name'] ?? '-') ?></td>
                        <td><?= isset($onu['distance']) ? $onu['distance'] . ' m' : '-' ?></td>
                        <td><?= isset($onu['tx_power']) ? $onu['tx_power'] . ' dBm' : '-' ?></td>
                        <td><?= isset($onu['rx_power']) ? $onu['rx_power'] . ' dBm' : '-' ?></td>
                        <td><?= isset($onu['download']) ? round($onu['download']/1073741824,2).' GB' : '-' ?></td>
                        <td><?= isset($onu['upload']) ? round($onu['upload']/1073741824,2).' GB' : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
    <?php
} else if ($vendor == 2){


    $oltIp = $ip;   
    $community = $community;

    // Needed OIDs
$oids = [
    'name'     => "1.3.6.1.2.1.2.2.1.2",
    'rx_power' => "1.3.6.1.4.1.3320.10.3.4.1.2",
    'tx_power' => "1.3.6.1.4.1.3320.10.3.4.1.3",
    'download' => "1.3.6.1.2.1.31.1.1.1.10", // ifHCInOctets
    'upload'   => "1.3.6.1.2.1.31.1.1.1.6",  // ifHCOutOctets
];

function snmpBulkWalk($community, $oltIp, $oids) {
    $data = [];
    foreach ($oids as $key => $oid) {
        // Fetch SNMP
        $cmd = "snmpbulkwalk -v2c -c $community -Cr400 -t 1 -r 1 $oltIp $oid 2>&1";
        $output = shell_exec($cmd);
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            if (empty($line)) continue;


            if (preg_match('/\.(\d+) = (?:STRING|INTEGER|Hex-STRING|Gauge32|Counter64|): ?"?(.+?)"?$/', $line, $matches)) {
                $index = $matches[1];
                $value = $matches[2];

                // Handle Rx/Tx power
                if (in_array($key, ['rx_power', 'tx_power'])) {
                    $value = (int)$value;
                    if ($value == -65535) $value = 0;
                    $value = $value / 10;
                }

                // Download/Upload in bytes
                if (in_array($key, ['download','upload'])) {
                    $value = (int)$value;
                }

                $data[$index][$key] = $value;
            }
        }
    }
    return $data;
}

$onuData = snmpBulkWalk($community, $oltIp, $oids);

$onuPorts = array_filter($onuData, function ($item) {
    return isset($item['name']) && preg_match('/^GPON\d+\/\d+:\d+$/', $item['name']);
});

uasort($onuPorts, function ($a, $b) {
    preg_match('/GPON(\d+)\/(\d+):(\d+)/', $a['name'], $m1);
    preg_match('/GPON(\d+)\/(\d+):(\d+)/', $b['name'], $m2);
    return [(int)$m1[1], (int)$m1[2], (int)$m1[3]] <=> [(int)$m2[1], (int)$m2[2], (int)$m2[3]];
});
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-info text-dark me-3">Total ONUs: <?= count($onuPorts) ?></span>
        <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th scope="col">SL</th>
                    <th scope="col">Interface</th>
                    <th scope="col">Tx Power (dBm)</th>
                    <th scope="col">Rx Power (dBm)</th>
                    <th scope="col">Download (GB)</th>
                    <th scope="col">Upload (GB)</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php $sl = 1; ?>
                <?php foreach ($onuPorts as $onu): ?>
                    <tr>
                        <td><?= $sl++ ?></td>
                        <td><?= htmlspecialchars($onu['name'] ?? '-') ?></td>
                        <td><?= isset($onu['tx_power']) ? $onu['tx_power'] . ' dBm' : '0 dBm' ?></td>
                        <td><?= isset($onu['rx_power']) ? $onu['rx_power'] . ' dBm' : '0 dBm' ?></td>
                        <td><?= isset($onu['download']) ? round($onu['download']/1073741824,2).' GB' : '0 GB' ?></td>
                        <td><?= isset($onu['upload']) ? round($onu['upload']/1073741824,2).' GB' : '0 GB' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
}else if($vendor == 3){

$oltIp = $ip;   
$community = $community;

// OIDs
$oidInterface = "1.3.6.1.4.1.37950.1.1.5.12.2.1.14.1.2"; // Interface Name
$oidTxPower = "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.6"; // TX
$oidRxPower = "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7"; // RX
$oidDistance = "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.13";

/**
 * SNMP Bulk Walk Helper
 */
function snmpWalkLines($ip, $community, $oid) {
    $cmd = "snmpbulkwalk -v2c -c $community -Cr20 -t 10 -r 5 $ip $oid 2>&1";
    $output = shell_exec($cmd);
    return explode("\n", trim($output));
}

// Fetch TX, RX and Distance
$linesName = snmpWalkLines($oltIp, $community, $oidInterface);
$linesTx = snmpWalkLines($oltIp, $community, $oidTxPower);
$linesRx = snmpWalkLines($oltIp, $community, $oidRxPower);
$linesDist = snmpWalkLines($oltIp, $community, $oidDistance);

    $parsedNames = [];
    foreach ($linesName as $line) {
        if (preg_match('/STRING:\s*"?([^"]*)"?$/', $line, $m)) {
            $parsedNames[] = trim($m[1]);
        } else {
            $parsedNames[] = "-";
        }
    }

    // Custom sort: EPON0/1:1, EPON0/1:2, EPON0/2:1, ...
    usort($parsedNames, function($a, $b) {
        if ($a === "-" || $b === "-") return $a === "-" ? 1 : -1;
        preg_match('/EPON(\d+)\/(\d+):(\d+)/', $a, $ma);
        preg_match('/EPON(\d+)\/(\d+):(\d+)/', $b, $mb);
        if (!$ma || !$mb) return strcmp($a, $b);
        for ($i = 1; $i <= 3; $i++) {
            if ((int)$ma[$i] !== (int)$mb[$i]) {
                return ((int)$ma[$i] < (int)$mb[$i]) ? -1 : 1;
            }
        }
        return 0;
    });

    $linesName = $parsedNames;


    // Parse results
    $interfaces = [];
    $maxCount = max(count($linesName), count($linesTx), count($linesRx), count($linesDist));

    for ($i = 0; $i < $maxCount; $i++) {
        
        $name = isset($linesName[$i]) ? $linesName[$i] : "-";

        // TX Power
        $tx = isset($linesTx[$i]) && preg_match('/STRING:\s*"?([^"]*)"?$/', $linesTx[$i], $mTx) ? trim($mTx[1]) : "-";

        // RX Power
        $rx = isset($linesRx[$i]) && preg_match('/STRING:\s*"?([^"]*)"?$/', $linesRx[$i], $mRx) ? trim($mRx[1]) : "-";

        // Distance
        $dist = isset($linesDist[$i]) && preg_match('/INTEGER:\s*(\d+)/', $linesDist[$i], $mDist) ? trim($mDist[1]) : "-";

        $interfaces[] = [
            'name' => $name,
            'tx'   => $tx,
            'rx'   => $rx,
            'dist' => $dist,
        ];
    }

?>

<!-- === HTML OUTPUT (Bootstrap 5) === -->
<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">🖧 ONU TX/RX Power & Distance</h5>
            <div>
                <span class="badge bg-info text-dark me-3">
                    Total ONUs: <?= count($interfaces) ?>
                </span>
                <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">
                    🔄 Refresh Data
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered align-middle mb-0">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>SL</th>
                            <th>ONU Name</th>
                            <th>TX Power</th>
                            <th>RX Power</th>
                            <th>Distance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl = 1; foreach ($interfaces as $if): ?>
                            <tr class="text-center">
                                <td><?= $sl++ ?></td>
                                <td><code class="fw-medium text-danger"><?= htmlspecialchars($if['name']) ?></code></td>
                                <td><span ><?= htmlspecialchars($if['tx']) ?></span></td>
                                <td><span ><?= htmlspecialchars($if['rx']) ?></span></td>
                                <td><span><?= htmlspecialchars($if['dist']) ?> m</span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($interfaces)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-danger">
                                    No data found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

}else if($vendor == 4){
    $oltIp = $ip;
    $community = $community;
// OIDs for interface info, RX & TX power
$oids = [
    'name'           => "1.3.6.1.2.1.2.2.1.2",        // Interface name
    'download_bytes' => "1.3.6.1.2.1.31.1.1.1.10",   // ifHCInOctets
    'upload_bytes'   => "1.3.6.1.2.1.31.1.1.1.6",    // ifHCOutOctets
    'rx_power'       => "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7", // RX power
    'tx_power'       => "1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.6", // TX power
];

// Function to run snmpbulkwalk
function snmpWalkLines($community, $oltIp, $oid) {
    $cmd = "snmpbulkwalk -v2c -c $community $oltIp $oid";
    $output = shell_exec($cmd);
    return explode("\n", trim($output));
}

// Step 1: Fetch interface names
$interfaces = [];
$lines = snmpWalkLines($community, $oltIp, $oids['name']);
foreach ($lines as $line) {
    if (preg_match('/\.(\d+) = STRING: "?(.+?)"?$/', $line, $matches)) {
        $interfaces[$matches[1]] = $matches[2]; // key = ifIndex
    }
}

// Step 2: Fetch download bytes
$downloads = [];
$lines = snmpWalkLines($community, $oltIp, $oids['download_bytes']);
foreach ($lines as $line) {
    if (preg_match('/\.(\d+) = Counter64: (\d+)/', $line, $matches)) {
        $downloads[$matches[1]] = (int)$matches[2];
    }
}

// Step 3: Fetch upload bytes
$uploads = [];
$lines = snmpWalkLines($community, $oltIp, $oids['upload_bytes']);
foreach ($lines as $line) {
    if (preg_match('/\.(\d+) = Counter64: (\d+)/', $line, $matches)) {
        $uploads[$matches[1]] = (int)$matches[2];
    }
}

// Step 4: Fetch RX power
$rxPowers = [];
$lines = snmpWalkLines($community, $oltIp, $oids['rx_power']);
foreach ($lines as $line) {
    if (preg_match('/(\d+)\.(\d+) = STRING: "?(.+?)"?$/', $line, $matches)) {
        $ponPort = $matches[1];
        $onuNo   = $matches[2];
        $rxPowers["$ponPort:$onuNo"] = $matches[3]; // e.g., "0.00 mW (-27.96 dBm)"
    }
}

// Step 5: Fetch TX power
$txPowers = [];
$lines = snmpWalkLines($community, $oltIp, $oids['tx_power']);
foreach ($lines as $line) {
    if (preg_match('/(\d+)\.(\d+) = STRING: "?(.+?)"?$/', $line, $matches)) {
        $ponPort = $matches[1];
        $onuNo   = $matches[2];
        $txPowers["$ponPort:$onuNo"] = $matches[3]; // e.g., "0.00 mW (-3.00 dBm)"
    }
}

// Step 6: Combine data by matching EPONx/y:z → PON port / ONU
$onuPorts = [];
foreach ($interfaces as $ifIndex => $name) {
    if (preg_match('/^EPON\d+\/(\d+):(\d+)$/', $name, $m)) {
        $ponPort = $m[1];
        $onuNo   = $m[2];
        $key     = "$ponPort:$onuNo";

        $onuPorts[] = [
            'name'           => $name,
            'download_bytes' => $downloads[$ifIndex] ?? null,
            'upload_bytes'   => $uploads[$ifIndex] ?? null,
            'rx_power'       => $rxPowers[$key] ?? null,
            'tx_power'       => $txPowers[$key] ?? null,
        ];
    }
}

// Step 7: Sort EPON interfaces logically
uasort($onuPorts, function ($a, $b) {
    preg_match('/EPON(\d+)\/(\d+):(\d+)/', $a['name'], $m1);
    preg_match('/EPON(\d+)\/(\d+):(\d+)/', $b['name'], $m2);
    return [$m1[1], $m1[2], $m1[3]] <=> [$m2[1], $m2[2], $m2[3]];
});
?>

<!-- HTML Output -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-info text-dark me-3">Total ONUs: <?= count($onuPorts) ?></span>
        <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th scope="col">SL</th>
                    <th scope="col">Interface</th>
                    <th scope="col">Download (GB)</th>
                    <th scope="col">Upload (GB)</th>
                    <th scope="col">RX Power</th>
                    <th scope="col">TX Power</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php $sl = 1; ?>
                <?php foreach ($onuPorts as $onu): ?>
                    <tr>
                        <td><?= $sl++ ?></td>
                        <td><?= htmlspecialchars($onu['name'] ?? '-') ?></td>
                        <td>
                            <?= isset($onu['download_bytes']) 
                                ? round($onu['download_bytes'] / 1073741824, 2) 
                                : '-' ?>
                        </td>
                        <td>
                            <?= isset($onu['upload_bytes']) 
                                ? round($onu['upload_bytes'] / 1073741824, 2) 
                                : '-' ?>
                        </td>
                        <td><?= htmlspecialchars($onu['rx_power'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($onu['tx_power'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


    <?php
}
else{
    echo "Invalid vendor";
}
