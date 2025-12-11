<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();


$mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $_POST["mikrotik_id"]], ['status', '=', '1']]]);

    if ($mk) {
        $mikrotik = new Mikrotik(
            $mk["mik_ip"],
            $mk["mik_port"],
            $mk["mik_username"],
            $mk["mik_password"]
        );

        if (!empty($mikrotik) && $mikrotik->connected) {
            $queues = $mikrotik->comm("/queue/simple/print", ["?name" => $_POST["name"]]);

            if (!empty($queues) && isset($queues[0][".id"])) {
                $id = $queues[0][".id"];
                $disableValue = ($_POST["status"] == 1) ? "no" : "yes";

                $mikrotik->comm("/queue/simple/set", [
                    ".id" => $id,
                    "disabled" => $disableValue
                ]);

                echo json_encode(true);
            }
        }
    }
    
    echo json_encode(false);