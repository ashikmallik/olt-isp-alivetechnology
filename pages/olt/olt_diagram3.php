<?php
$oltIp = "103.129.211.73:50501";
$community = "public";

// =====================
// Initialize interfaces array
// =====================
$interfaces = [];

// =====================
// OIDs
// =====================
$oidIfDescr      = "1.3.6.1.2.1.2.2.1.2";        // Interface Name
$oidIfOperStatus = "1.3.6.1.2.1.2.2.1.8";        // Interface Status
$oidIfMac        = "1.3.6.1.2.1.2.2.1.6";        // MAC Address
$oidTxPower      = "1.3.6.1.4.1.50224.3.3.3.1.4"; // TX Power
$oidRxPower      = "1.3.6.1.4.1.50224.3.3.3.1.5"; // RX Power

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
    if (preg_match('/\.(\d+)\.(\d+)\.0\.0\s*=\s*INTEGER:\s*(-?\d+)/', $line, $m)) {
        $onuIndex = $m[2]; // 16779289 etc
        if (!isset($interfaces[$onuIndex])) continue;
        $interfaces[$onuIndex]['tx'] = $m[3]; // value as is
    }
}

// =====================
// Parse RX Power
// =====================
foreach ($linesRx as $line) {
    if (preg_match('/\.(\d+)\.(\d+)\.0\.0\s*=\s*INTEGER:\s*(-?\d+)/', $line, $m)) {
        $onuIndex = $m[2];
        if (!isset($interfaces[$onuIndex])) continue;
        $interfaces[$onuIndex]['rx'] = $m[3];
    }
}
?>

<!-- ===================== HTML Table ===================== -->
<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">🖧 ONU Interface List (Name + Status + MAC + TX/RX)</h5>
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
                            <th>Status</th>
                            <th>MAC</th>
                            <th>TX Power</th>
                            <th>RX Power</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl=1; foreach ($interfaces ?? [] as $index=>$d): ?>
                            <tr class="text-center">
                                <td><?= $sl++ ?></td>
                                <td><code><?= htmlspecialchars($d['name'] ?? '-') ?></code></td>

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

