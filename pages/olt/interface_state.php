<?php
set_time_limit(300);

$id        = $_GET['id'] ?? null;
$vendor    = $_GET['vendor'] ?? null;
$ip        = $_GET['ip'] ?? null;
$community = $_GET['community'] ?? null;


if($vendor == 1){

$oltIp = $ip;
$community = $community;

// OIDs
$oidIfDescr      = "1.3.6.1.2.1.2.2.1.2";        // Interface Name
$oidIfOperStatus = "1.3.6.1.2.1.2.2.1.8";        // Operational Status
$oidVendorId     = "1.3.6.1.4.1.3320.101.10.1.1.1"; // Vendor ID
$oidSerial       = "1.3.6.1.4.1.3320.101.10.1.1.3"; // Serial Number
$oidOnuUpTime    = "1.3.6.1.2.1.2.2.1.9";           // Uptime (Timeticks)

// Function to convert Hex-STRING to readable serial
function hexToSerial($hexString) {
    $hexString = preg_replace('/[^0-9A-Fa-f]/', '', $hexString);
    return strtoupper(implode(':', str_split($hexString, 2)));
}

// Function to convert Timeticks to human-readable
function ticksToTime($ticks) {
    $seconds = (int)($ticks / 100);
    $days    = floor($seconds / 86400);
    $hours   = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return "{$days}d {$hours}h {$minutes}m";
}

// SNMP fetch all lines
$linesName   = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidIfDescr 2>&1")));
$linesStatus = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidIfOperStatus 2>&1")));
$linesVendor = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidVendorId 2>&1")));
$linesSerial = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidSerial 2>&1")));
$linesUpTime = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidOnuUpTime 2>&1")));

// Parsed Interface Names
$interfaces = [];
foreach ($linesName as $line) {
    if (preg_match('/\.(\d+)\s*=\s*STRING:\s*"?(.*?)"?$/', $line, $m)) {
        $index = $m[1];
        $interfaces[$index]['name'] = $m[2];
    }
}

// Parsed Status
$statusMap = [1=>'Up',2=>'Down',3=>'Testing',4=>'Unknown',5=>'Dormant',6=>'Not Present',7=>'Lower Layer Down'];
foreach ($linesStatus as $line) {
    if (preg_match('/\.(\d+)\s*=\s*INTEGER:\s*(\d+)/', $line, $m)) {
        $index = $m[1];
        $code  = (int)$m[2];
        $interfaces[$index]['status'] = $statusMap[$code] ?? 'Unknown';
    }
}

// Parsed Vendor ID
foreach ($linesVendor as $line) {
    if (preg_match('/\.(\d+)\s*=\s*STRING:\s*"?(.*?)"?$/', $line, $m)) {
        $index = $m[1];
        $interfaces[$index]['vendor_id'] = $m[2];
    }
}

// Parsed Serial Number
foreach ($linesSerial as $line) {
    if (preg_match('/\.(\d+)\s*=\s*Hex-STRING:\s*(.+)/', $line, $m)) {
        $index = $m[1];
        $interfaces[$index]['serial_number'] = hexToSerial($m[2]);
    } elseif (preg_match('/\.(\d+)\s*=\s*STRING:\s*"?(.*?)"?$/', $line, $m)) {
        $index = $m[1];
        $interfaces[$index]['serial_number'] = $m[2];
    }
}

// Parsed Uptime
foreach ($linesUpTime as $line) {
    if (preg_match('/\.(\d+)\s*=\s*Timeticks:\s*\((\d+)\)/', $line, $m)) {
        $index = $m[1];
        $ticks = (int)$m[2];
        $interfaces[$index]['uptime'] = ticksToTime($ticks);
    }
}

// Filter only EPON interfaces
$onuPorts = array_filter($interfaces, fn($d) => isset($d['name']) && preg_match('/^EPON\d+\/\d+[:\.-]\d+$/', $d['name']));

// Sort logically
uasort($onuPorts, function ($a, $b) {
    preg_match('/EPON(\d+)\/(\d+)[:\.-](\d+)/', $a['name'], $aMatch);
    preg_match('/EPON(\d+)\/(\d+)[:\.-](\d+)/', $b['name'], $bMatch);
    return [(int)$aMatch[1], (int)$aMatch[2], (int)$aMatch[3]] <=> [(int)$bMatch[1], (int)$bMatch[2], (int)$bMatch[3]];
});
?>

<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">🖧 EPON Ports Full Details</h5>
            <div>
                <span class="badge bg-info text-dark me-3">Total EPON: <?= count($onuPorts) ?></span>
                <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
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
                            <th>Interface Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Vendor</th>
                            <th>Serial Number</th>
                            <th>Uptime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl = 1; foreach ($onuPorts as $onu): ?>

                        <?php
                            $serial = isset($onu['serial_number']) ? trim($onu['serial_number']) : '';

                            $agent = $obj->details_by_cond('tbl_agent', "onumac = '$serial'");

                            $customerName = $agent['ag_name'] ?? '-';
                            $customerIp   = $agent['ip']     ?? '-';
                            $agentId      = $agent['ag_id']  ?? '-';
                            $customerId   = $agent['cus_id'] ?? '-';
                        ?>


                            <tr class="text-center">
                                <td><?= $sl++ ?></td>
                                <td><code><?= htmlspecialchars($onu['name']) ?></code></td>


                                <td class="text-center align-middle">
                                    <!-- Customer Name -->
                                    <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none fw-semibold">
                                        <?= htmlspecialchars((string)$customerName, ENT_QUOTES, 'UTF-8') ?>
                                    </a>

                                    <!-- IP -->
                                    <br>
                                    <small class="text-muted d-block mb-1">
                                        <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars((string)$customerIp, ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </small>

                                    <!-- Button with customerId (cus_id) -->
                                    <?php if (!empty($customerId) && $customerId !== '-'): ?>
                                        <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>"
                                        class="btn btn-info btn-sm px-2 py-1 waves-effect waves-light">
                                            View
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm px-2 py-1" disabled>No Customer</button>
                                    <?php endif; ?>
                                </td>




                                <td>
                                    <?php
                                        $status = $onu['status'] ?? 'Unknown';
                                        $badgeClass = match ($status) {
                                            'Up' => 'bg-success',
                                            'Down' => 'bg-danger',
                                            'Testing' => 'bg-warning text-dark',
                                            'Dormant', 'Lower Layer Down' => 'bg-secondary',
                                            default => 'bg-dark',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                                <td><?= htmlspecialchars($onu['vendor_id'] ?? '-') ?></td>
                                <td><code><?= htmlspecialchars($onu['serial_number'] ?? '-') ?></code></td>
                                <td><?= htmlspecialchars($onu['uptime'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($onuPorts)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-danger">No EPON interfaces found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

}else if ($vendor == 2){

$oltIp = $ip;
$community = $community;
// === OIDs ===
$oidIfDescr      = "1.3.6.1.2.1.2.2.1.2";        // Interface Name
$oidIfOperStatus = "1.3.6.1.2.1.2.2.1.8";        // Interface Status (1=Up, 2=Down)
$oidIfUpTime     = "1.3.6.1.2.1.2.2.1.9";        // Uptime (Timeticks)
$oidVendor       = "1.3.6.1.4.1.3320.10.2.6.1.3";  // Vendor info

// === Convert Timeticks to human-readable ===
function ticksToTime($ticks) {
    if (!is_numeric($ticks) || $ticks < 0) {
        return '-';
    }
    $seconds = (int)($ticks / 100);
    $days    = floor($seconds / 86400);
    $hours   = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return sprintf("%dd %02dh %02dm", $days, $hours, $minutes);
}

// === Fetch SNMP Data ===
function snmpBulk($oid) {
    global $oltIp, $community;

    return explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oid 2>&1")));
}

$linesName       = snmpBulk($oidIfDescr);
$linesOperStatus = snmpBulk($oidIfOperStatus);
$linesUpTime     = snmpBulk($oidIfUpTime);
$linesVendor     = snmpBulk($oidVendor);

$interfaces = [];
$ponPortIndexes = [];
$vendors = [];

// === Parse Interface Names ===
foreach ($linesName as $line) {
    if (preg_match('/\.(\d+)\s*=\s*STRING:\s*"?([^"]+)"?/', $line, $m)) {
        $index = (int)$m[1];
        $name  = trim($m[2]);
        $interfaces[$index]['name'] = $name;

        if (preg_match('/^(GPON\d+\/\d+)$/', $name, $ponMatch)) {
            $ponPortIndexes[$ponMatch[0]] = $index;
        }
    }
}

// === Parse Operational Status ===
foreach ($linesOperStatus as $line) {
    if (preg_match('/\.(\d+)\s*=\s*INTEGER:\s*\w*\(?(\d+)\)?/', $line, $m)) {
        $index  = (int)$m[1];
        $status = (int)$m[2];
        $interfaces[$index]['status'] = match ($status) {
            1 => 'Up',
            2 => 'Down',
            default => 'Unknown'
        };
    }
}

// === Parse Uptime ===
foreach ($linesUpTime as $line) {
    if (preg_match('/\.(\d+)\s*=\s*Timeticks:\s*\((\d+)\)/', $line, $m)) {
        $index = (int)$m[1];
        $ticks = (int)$m[2];
        $interfaces[$index]['uptime'] = ticksToTime($ticks);
    }
}

// === Parse Vendor Info ===
foreach ($linesVendor as $line) {
    if (preg_match('/\.(\d+)\.(\d+)\s*=\s*STRING:\s*"([^"]+)"/', $line, $m)) {
        $ponIfIndex = (int)$m[1];
        $onuId      = (int)$m[2];
        $vendorInfo = trim($m[3]);
        $vendors[$ponIfIndex][$onuId] = $vendorInfo;
    }
}

// === Filter Only GPON Interfaces ===
$onuPorts = array_filter($interfaces, fn($d) =>
    isset($d['name']) && preg_match('/^GPON\d+\/\d+[:\.-]\d+$/', $d['name'])
);

// === Sort Logically by GPON Position ===
uasort($onuPorts, function ($a, $b) {
    preg_match('/GPON(\d+)\/(\d+)[:\.-](\d+)/', $a['name'], $aMatch);
    preg_match('/GPON(\d+)\/(\d+)[:\.-](\d+)/', $b['name'], $bMatch);
    return [(int)$aMatch[1], (int)$aMatch[2], (int)$aMatch[3]] <=> [(int)$bMatch[1], (int)$bMatch[2], (int)$bMatch[3]];
});
?>
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 0.75rem; }
        .table-hover tbody tr:hover { background-color: #e9ecef; }
        .table th { font-weight: 600; }
        code { color: #d63384; font-size: 0.9em; }
    </style>

<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">🖧 GPON Ports Full Details</h5>
            <div>
                <span class="badge bg-info text-dark me-3 fs-6">Total GPON: <?= count($onuPorts) ?></span>
                <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>SL</th>
                            <th>Interface Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Vendor</th>
                            <th>Uptime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($onuPorts)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-danger fw-bold py-4">No GPON interfaces found</td>
                            </tr>
                        <?php else: ?>
                            <?php $sl = 1; foreach ($onuPorts as $onu): ?>

                        <?php
                            $serial = isset($onu['serial_number']) ? trim($onu['serial_number']) : '';

                            $agent = $obj->details_by_cond('tbl_agent', "onumac = '$serial'");

                            $customerName = $agent['ag_name'] ?? '-';
                            $customerIp   = $agent['ip']     ?? '-';
                            $agentId      = $agent['ag_id']  ?? '-';
                            $customerId   = $agent['cus_id'] ?? '-';

                        ?>
                        <tr class="text-center">
                            <td><?= $sl++ ?></td>
                            <td><code><?= htmlspecialchars($onu['name']) ?></code></td>


                            <td class="text-center align-middle">
                                <!-- Customer Name -->
                                <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none fw-semibold">
                                    <?= htmlspecialchars((string)$customerName, ENT_QUOTES, 'UTF-8') ?>
                                </a>

                                <!-- IP -->
                                <br>
                                <small class="text-muted d-block mb-1">
                                    <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars((string)$customerIp, ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </small>

                                <!-- Button with customerId (cus_id) -->
                                <?php if (!empty($customerId) && $customerId !== '-'): ?>
                                    <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>"
                                    class="btn btn-info btn-sm px-2 py-1 waves-effect waves-light">
                                        View
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm px-2 py-1" disabled>No Customer</button>
                                <?php endif; ?>
                            </td>

                                    <td>
                                        <?php
                                        $status = $onu['status'] ?? 'Unknown';
                                        $badge = match ($status) {
                                            'Up' => 'success',
                                            'Down' => 'danger',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $vendorDisplay = '-';
                                        if (preg_match('/^(GPON\d+\/\d+)[:\.-](\d+)$/', $onu['name'], $nameParts)) {
                                            $ponName = $nameParts[1];
                                            $onuId   = (int)$nameParts[2];

                                            if (isset($ponPortIndexes[$ponName])) {
                                                $ponIfIndex = $ponPortIndexes[$ponName];

                                                if (isset($vendors[$ponIfIndex][$onuId])) {
                                                    $vendorDisplay = htmlspecialchars($vendors[$ponIfIndex][$onuId]);
                                                }
                                            }
                                        }
                                        echo $vendorDisplay;
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($onu['uptime'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php


}else if ($vendor == 3){

$oltIp = $ip;
$community = $community;

// === OIDs ===
$oidIfDescr      = "1.3.6.1.2.1.2.2.1.2"; // Interface Name
$oidIfOperStatus = "1.3.6.1.2.1.2.2.1.8"; // Interface Status (1=up, 2=down)
$oidIfMac        = "1.3.6.1.2.1.2.2.1.6"; // MAC Address
$oidIfLastChange = "1.3.6.1.2.1.2.2.1.9"; // Interface Last Change (Timeticks)

// === SNMP Fetch Function ===
function snmpWalkLines($ip, $community, $oid) {
    $cmd = "snmpbulkwalk -v2c -c $community -Cr20 -t 8 -r 5 $ip $oid 2>&1";
    $output = shell_exec($cmd);
    return explode("\n", trim($output));
}

// === Fetch Data ===
$linesName       = snmpWalkLines($oltIp, $community, $oidIfDescr);
$linesStatus     = snmpWalkLines($oltIp, $community, $oidIfOperStatus);
$linesMac        = snmpWalkLines($oltIp, $community, $oidIfMac);
$linesLastChange = snmpWalkLines($oltIp, $community, $oidIfLastChange);

$interfaces = [];

// === Parse Interface Names ===
foreach ($linesName as $line) {
    if (preg_match('/\.(\d+)\s*=\s*STRING:\s*"?(.*?)"?$/', $line, $m)) {
        $index = $m[1];
        $interfaces[$index]['name'] = trim($m[2]);
    }
}

// === Parse Interface Status ===
foreach ($linesStatus as $line) {
    if (preg_match('/\.(\d+)\s*=\s*INTEGER:\s*(\d+)/', $line, $m)) {
        $index = $m[1];
        $interfaces[$index]['status'] = ($m[2] == 1) ? 'Up' : 'Down';
    }
}

// === Parse MAC Addresses ===
foreach ($linesMac as $line) {
    if (preg_match('/\.(\d+)\s*=\s*Hex-STRING:\s*(.*)/', $line, $m)) {
        $index = $m[1];
        $hex = trim($m[2]);
        if ($hex) {
            $mac = implode(':', array_map(function($h){ return str_pad($h,2,'0',STR_PAD_LEFT); }, explode(' ', $hex)));
            $interfaces[$index]['mac'] = strtoupper($mac);
        } else {
            $interfaces[$index]['mac'] = 'N/A';
        }
    }
}

// === Parse Last Change / Uptime ===
foreach ($linesLastChange as $line) {
    if (preg_match('/\.(\d+)\s*=\s*Timeticks:\s*\((\d+)\)/', $line, $m)) {
        $index = $m[1];
        $ticks = intval($m[2]); // 1 tick = 1/100 second
        $seconds = intval($ticks / 100);

        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $interfaces[$index]['uptime'] = "{$days}d {$hours}h {$minutes}m {$secs}s";
    }
}

// === Filter + Sort Interfaces ===
$validInterfaces = array_filter($interfaces, function ($d) {
    return isset($d['name']) && preg_match('/(PON|EPON|GE|ONU|GPON|epon|pon|ge|onu)/i', $d['name']);
});

usort($validInterfaces, function ($a, $b) {
    $order = ['PON' => 1, 'EPON' => 1, 'GPON' => 1, 'GE' => 2, 'ONU' => 3];
    preg_match('/(PON|EPON|GPON|GE|ONU)/i', $a['name'], $mA);
    preg_match('/(PON|EPON|GPON|GE|ONU)/i', $b['name'], $mB);
    $typeA = $order[strtoupper($mA[1] ?? '')] ?? 99;
    $typeB = $order[strtoupper($mB[1] ?? '')] ?? 99;
    return $typeA === $typeB ? strnatcmp($a['name'], $b['name']) : $typeA - $typeB;
});
?>

<!-- === HTML OUTPUT === -->
<div class="container py-4">
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
      <h5 class="mb-0 text-primary fw-bold">🖧 OLT Interface List</h5>
      <div>
        <span class="badge bg-info text-dark me-3">
          Total Interfaces: <?= count($validInterfaces) ?>
        </span>
        <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">
          🔄 Refresh
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
              <th>Interface Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>MAC Address</th>
              <th>Up Time</th>
            </tr>
          </thead>
          <tbody>
            <?php $sl = 1; foreach ($validInterfaces as $if): ?>

            <?php
                $serial = isset($if['mac']) ? trim($if['mac']) : '';

                $agent = $obj->details_by_cond('tbl_agent', "onumac = '$serial'");

                $customerName = $agent['ag_name'] ?? '-';
                $customerIp   = $agent['ip']     ?? '-';
                $agentId      = $agent['ag_id']  ?? '-';
                $customerId   = $agent['cus_id'] ?? '-';
            ?>

              <tr class="text-center">
                <td><?= $sl++ ?></td>
                <td><code><?= htmlspecialchars($if['name']) ?></code></td>


            <td class="text-center align-middle">
                <!-- Customer Name -->
                <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none fw-semibold">
                    <?= htmlspecialchars((string)$customerName, ENT_QUOTES, 'UTF-8') ?>
                </a>

                <!-- IP -->
                <br>
                <small class="text-muted d-block mb-1">
                    <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none">
                        <?= htmlspecialchars((string)$customerIp, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </small>

                <!-- Button with customerId (cus_id) -->
                <?php if (!empty($customerId) && $customerId !== '-'): ?>
                    <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>"
                    class="btn btn-info btn-sm px-2 py-1 waves-effect waves-light">
                        View
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm px-2 py-1" disabled>No Customer</button>
                <?php endif; ?>
            </td>



                <td>
                  <?php if (!empty($if['status'])): ?>
                    <span class="badge <?= $if['status'] === 'Up' ? 'bg-success' : 'bg-danger' ?>">
                      <?= $if['status'] ?>
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary">N/A</span>
                  <?php endif; ?>
                </td>
                <td><code><?= $if['mac'] ?? 'N/A' ?></code></td>
                <td><?= $if['uptime'] ?? 'N/A' ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($validInterfaces)): ?>
              <tr>
                <td colspan="5" class="text-center text-danger">No interfaces found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php


}else if ($vendor == 4){
$oltIp = $ip;
$community = $community;

// === OIDs ===
$oidIfDescr      = "1.3.6.1.2.1.2.2.1.2";
$oidIfOperStatus = "1.3.6.1.2.1.2.2.1.8";
$oidOnuUpTime    = "1.3.6.1.2.1.2.2.1.9";
$oidMacAddr      = "1.3.6.1.4.1.37950.1.1.5.12.1.12.1.6";

// === SNMP Fetch Function ===
function snmpFetch($oid)
{
    global $oltIp, $community;
    $cmd = "snmpwalk -v2c -c $community -t 4 -r 1 $oltIp $oid";
    $out = shell_exec($cmd);
    return $out ? explode("\n", trim($out)) : [];
}

// === Fetch Data ===
$descrLines  = snmpFetch($oidIfDescr);
$statusLines = snmpFetch($oidIfOperStatus);
$uptimeLines = snmpFetch($oidOnuUpTime);
$macLines    = snmpFetch($oidMacAddr);

$interfaceData = [];

// --- Interface Name ---
foreach ($descrLines as $line) {
    if (preg_match('/\.(\d+) = STRING: (.+)/', $line, $m)) {
        $interfaceData[$m[1]] = [
            'name' => trim($m[2], '"')
        ];
    }
}

// --- Interface Status (FIXED) ---
$statusMap = [
    1 => 'Connected',
    2 => 'Down',
    3 => 'Testing',
    4 => 'Unknown',
    5 => 'Dormant',
    6 => 'Not Present',
    7 => 'Lower Layer Down'
];

foreach ($statusLines as $line) {
    // Updated regex to handle both formats: INTEGER: 1 or INTEGER: up(1)
    if (preg_match('/\.(\d+) = INTEGER: (?:\w+\()?(\d+)\)?/', $line, $m)) {
        $index = $m[1];
        $statusCode = (int)$m[2];
        $interfaceData[$index]['status'] = $statusMap[$statusCode] ?? 'Unknown';
    }
}

// --- Interface Uptime ---
foreach ($uptimeLines as $line) {
    if (preg_match('/\.(\d+) = Timeticks: \((\d+)\)/', $line, $m)) {
        $sec = (int)$m[2] / 100;
        $days = floor($sec / 86400);
        $hours = floor(($sec % 86400) / 3600);
        $mins = floor(($sec % 3600) / 60);
        $interfaceData[$m[1]]['uptime'] = sprintf("%dd %dh %dm", $days, $hours, $mins);
    }
}

// --- Filter only ONUs (EPONx/x:x)
$onuPorts = array_filter($interfaceData, fn($d) =>
    isset($d['name']) && preg_match('/^EPON\d+\/\d+:\d+$/', $d['name'])
);

// --- Sort ONUs by EPON port ---
uasort($onuPorts, function ($a, $b) {
    preg_match('/EPON(\d+)\/(\d+):(\d+)/', $a['name'], $x);
    preg_match('/EPON(\d+)\/(\d+):(\d+)/', $b['name'], $y);
    return [$x[1], $x[2], $x[3]] <=> [$y[1], $y[2], $y[3]];
});

// --- MAC Address Mapping ---
$macList = [];
foreach ($macLines as $line) {
    if (preg_match('/= STRING: "?([0-9A-Fa-f: -]+)"?$/', $line, $m)) {
        $macList[] = trim($m[1]);
    }
}

$onuKeys = array_keys($onuPorts);
foreach ($onuKeys as $i => $key) {
    $onuPorts[$key]['mac_addr'] = $macList[$i] ?? '-';
}

// === Debug if no data ===
if (empty($onuPorts)) {
    echo "<div class='alert alert-warning text-center mt-4'>
            ⚠ No ONU data found!<br>
            <code>Check OLT IP: $oltIp | Community: $community</code>
          </div>";
}
?>

<div class="container py-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">🖧 ONU Port Overview</h5>
            <div>
                <span class="badge bg-info text-dark me-3">Total ONUs: <?= count($onuPorts) ?></span>
                <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
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
                            <th>Interface</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>MAC Address</th>
                            <th>Uptime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($onuPorts)): ?>
                            <tr><td colspan="6" class="text-center text-danger">⚠ No ONU data found</td></tr>
                        <?php else: ?>
                            <?php $sl = 1; foreach ($onuPorts as $onu): ?>
                                <tr class="text-center">
                                    <td><?= $sl++ ?></td>
                                    <td><code><?= htmlspecialchars($onu['name']) ?></code></td>

                                    <td>
                                        <?php
                                        $mac = $onu['mac_addr'] ?? '';
                                        if (isset($obj) && !empty($mac)) {
                                            $agent = $obj->details_by_cond('tbl_agent', "onumac = '$mac'");
                                            $customerName = $agent['ag_name'] ?? '-';
                                            $customerIp = $agent['ip'] ?? '-';
                                            $agentId = $agent['ag_id'] ?? '-';
                                            $customerId = $agent['cus_id'] ?? '-';
                                        } else {
                                            $customerName = '-';
                                            $customerIp = '-';
                                            $agentId = '-';
                                            $customerId = '-';
                                        }
                                        ?>

                                        <a href="?page=customer_ledger&token=<?= urlencode($agentId) ?>"
                                           class="text-decoration-none fw-semibold">
                                           <?= htmlspecialchars($customerName) ?>
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($customerIp) ?>
                                        </small><br>

                                        <?php if ($customerId !== '-' && $customerId != ''): ?>
                                            <a href="?page=customer_ledger&token=<?= urlencode($agentId) ?>"
                                               class="btn btn-info btn-sm px-2 py-1">View</a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm px-2 py-1" disabled>No Customer</button>
                                        <?php endif; ?>
                                    </td>

                                    <?php
                                    $status = $onu['status'] ?? 'Unknown';
                                    $badgeClass = match ($status) {
                                        'Connected' => 'bg-success',
                                        'Down' => 'bg-danger',
                                        'Testing' => 'bg-warning text-dark',
                                        'Dormant', 'Lower Layer Down' => 'bg-secondary',
                                        default => 'bg-dark',
                                    };
                                    ?>
                                    <td><span class="badge <?= $badgeClass ?>"><?= $status ?></span></td>
                                    <td><code><?= $onu['mac_addr'] ?? '-' ?></code></td>
                                    <td><?= $onu['uptime'] ?? '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php

}elseif ($vendor == 5){

$oltIp = $ip;
$community = $community;


$interfaces = [];

$oidIfDescr      = "1.3.6.1.2.1.2.2.1.2";        // Interface Name
$oidIfOperStatus = "1.3.6.1.2.1.2.2.1.8";        // Interface Status
$oidIfMac        = "1.3.6.1.2.1.2.2.1.6";        // MAC Address
// $oidTxPower      = "1.3.6.1.4.1.50224.3.3.3.1.4"; // TX Power
// $oidRxPower      = "1.3.6.1.4.1.50224.3.3.3.1.5"; // RX Power
$oidRxPower      = "1.3.6.1.4.1.17409.2.3.6.1.1.11";  
$oidTxPower      = "1.3.6.1.4.1.17409.2.3.6.1.1.12"; 

// =====================
// Fetch SNMP data
// =====================
$linesName   = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidIfDescr 2>&1")));
$linesStatus = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidIfOperStatus 2>&1")));
$linesMac    = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidIfMac 2>&1")));
$linesTx     = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidTxPower 2>&1")));
$linesRx     = explode("\n", trim(shell_exec("snmpbulkwalk -v2c -c $community -t 2 -r 2 $oltIp $oidRxPower 2>&1")));

// =====================
// Parse Interface Names
// =====================
foreach ($linesName as $line) {
    if (preg_match('/\.(\d+)\s*=\s*STRING:\s*"?(.+?)"?$/', $line, $m)) {
        $index = $m[1];
        $name  = $m[2];
        if (!preg_match('/^ONU/i', $name)) continue; // Only ONU
        $interfaces[$index]['name'] = $name;
    }
}

// =====================
// Parse Status
// =====================
$statusMap = [
    1 => 'Up',
    2 => 'Down',
    3 => 'Testing',
    4 => 'Unknown',
    5 => 'Dormant',
    6 => 'Not Present',
    7 => 'Lower Layer Down'
];

foreach ($linesStatus as $line) {
    if (preg_match('/\.(\d+)\s*=\s*INTEGER:\s*(\d+)/', $line, $m)) {
        $index = $m[1];
        if (!isset($interfaces[$index])) continue;
        $interfaces[$index]['status'] = $statusMap[$m[2]] ?? 'Unknown';
    }
}

// =====================
// Parse MAC Address
// =====================
foreach ($linesMac as $line) {
    if (preg_match('/\.(\d+)\s*=\s*Hex-STRING:\s*(.+)$/', $line, $m)) {
        $index = $m[1];
        if (!isset($interfaces[$index])) continue;
        $mac = strtolower(str_replace(' ', ':', trim($m[2])));
        $interfaces[$index]['mac'] = $mac;
    }
}

// =====================
// Parse TX Power
// =====================
foreach ($linesTx as $line) {
    if (preg_match('/\.(\d+)\.(\d+)\.(\d+)\s*=\s*INTEGER:\s*(-?\d+)/', $line, $m)) {

        $onuIndex = $m[3]; 
        $value = $m[4] / 10; // convert to dBm

        if (!isset($interfaces[$onuIndex])) continue;
        $interfaces[$onuIndex]['tx'] = $value;
    }
}

foreach ($linesRx as $line) {
    if (preg_match('/\.(\d+)\.(\d+)\.(\d+)\s*=\s*INTEGER:\s*(-?\d+)/', $line, $m)) {

        $onuIndex = $m[3];
        $value = $m[4] / 10;

        if (!isset($interfaces[$onuIndex])) continue;
        $interfaces[$onuIndex]['rx'] = $value;
    }
}
?>

<!-- ===================== HTML Table ===================== -->
<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">🖧 ONU Interface List (Name + Status + MAC + TX/RX )</h5>
            <button onclick="location.reload()" class="btn btn-sm btn-outline-primary">🔄 Refresh</button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered align-middle mb-0">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>SL</th>
                            <th>ONU Interface</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>MAC</th>
                            <th>TX Power</th>
                            <th>RX Power</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl=1; foreach ($interfaces ?? [] as $index=>$d): ?>
                            <?php
                            $serial = isset($onu['mac']) ? trim($onu['mac']) : '';

                            $agent = $obj->details_by_cond('tbl_agent', "onumac = '$serial'");

                            $customerName = $agent['ag_name'] ?? '-';
                            $customerIp   = $agent['ip']     ?? '-';
                            $agentId      = $agent['ag_id']  ?? '-';
                            $customerId   = $agent['cus_id'] ?? '-';
                        ?>
                            <tr class="text-center">
                                <td><?= $sl++ ?></td>
                                <td><code><?= htmlspecialchars($d['name'] ?? '-') ?></code></td>

                                <td class="text-center align-middle">
                                    <!-- Customer Name -->
                                    <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none fw-semibold">
                                        <?= htmlspecialchars((string)$customerName, ENT_QUOTES, 'UTF-8') ?>
                                    </a>

                                    <!-- IP -->
                                    <br>
                                    <small class="text-muted d-block mb-1">
                                        <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars((string)$customerIp, ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </small>

                                    <!-- Button with customerId (cus_id) -->
                                    <?php if (!empty($customerId) && $customerId !== '-'): ?>
                                        <a href="?page=customer_ledger&token=<?= urlencode((string)$agentId) ?>"
                                        class="btn btn-info btn-sm px-2 py-1 waves-effect waves-light">
                                            View
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm px-2 py-1" disabled>No Customer</button>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php
                                        $status = $d['status'] ?? 'Unknown';
                                        $badgeClass = match($status){
                                            'Up'=>'bg-success',
                                            'Down'=>'bg-danger',
                                            'Testing'=>'bg-warning text-dark',
                                            default=>'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>

                                <td><code><?= $d['mac'] ?? '-' ?></code></td>
                                <td><?= isset($d['tx']) ? $d['tx'].' dBm' : '-' ?></td>
                                <td><?= isset($d['rx']) ? $d['rx'].' dBm' : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if(empty($interfaces)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-danger">No ONU interfaces found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

}


else{
    echo "Invalid vendor";
}
