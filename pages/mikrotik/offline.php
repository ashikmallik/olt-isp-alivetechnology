<?php

$mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');

// $mikrotik = new Mikrotik($mikrotikLoginData["mik_ip"], $mikrotikLoginData["mik_port"], $mikrotikLoginData["mik_username"], $mikrotikLoginData["mik_password"]);



$notification = "";
//taking month and years
$day = date('M-Y');



$singleMikrotikAgent = $obj->view_all('tbl_agent');
$agentList = [];
 if(!empty($singleMikrotikAgent)){
     //all agent data set to array
     foreach($singleMikrotikAgent as $agent){
         $agentList[$agent['ip']] = $agent['ip'];
     }
 }
 

 
 
    $allSecretData = $obj->viewAllPppSecret($mikrotikLoginData["id"]);

    
   
    $allRunnigSecretData = $obj->interfaceStatus($mikrotikLoginData["id"]);
    $agentListsecret = [];
     if(!empty($allRunnigSecretData)){
         foreach($allRunnigSecretData as $agentsecon){
            $agentsecon['name'] = trim(str_replace('<pppoe-','',$agentsecon['name']),'>');
            $agentListsecret[$agentsecon['name']] = $agentsecon['name'];
         }
     }
        

    
    
     
    ?>
    <div class="row">
        <div class="col-md-12 col-md-offset-2 margin_15_px">
            <div class="alert alert-success text-center">
                <strong>Welcome To Mikrotik</strong>
                <br>
                <small>Successfully connected to Mikrotik Router by IP
                    :<?php echo $mikrotikLoginData['mik_ip']; ?></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mikrotikTable table-responsive">
           <div class="panel panel-default">
               <h4 class="text-center"> Microtik Offline Information</h4>
           </div>
            <table class="table table-responsive table-bordered table-hover table-striped" id="example">
                <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Pasword</th>
                    <th class="text-center">Profile</th>
                    <th class="text-center">Service</th>
                    <th class="text-center">Disable Status</th>
                    <th class="text-center">Last Log out</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;

                foreach ($allSecretData as $secretData) {
                    if(@$agentList[$secretData['name']] != $secretData['name']){
                        continue;
                    }
                    
                    if(@$agentListsecret[$secretData['name']] == $secretData['name']){
                        continue;
                    }
                    
                    
                    
                    ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td><?php echo $secretData['name'] ?></td>
                            <td><?php echo $secretData['password'] ?></td>
                            <td><?php echo $secretData['profile'] ?></td>
                            <td><?php echo $secretData['service'] ?></td>
                            <td class="text-center"><?php echo ($secretData['disabled'] == 'false') ? '-' : 'Disabled' ?></td>
                            <td class="text-center">
                                <?php
                                if(isset($secretData['last-logged-out'])){

                                    if(($secretData['last-logged-out'] != 'jan/01/1970 00:00:00')){
                                        echo ucfirst($secretData['last-logged-out']);
                                    }else{
                                        echo '-';
                                    }
                                } ?>
                            </td>
                            <td>
                                <?php
                                 if ($secretData['disabled'] == 'false') {
                                
                                    echo '<button id="secretCangeStatus" data-status="1" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Offline</button>';
                                } else {
                                    echo'<button id="secretCangeStatus"  data-status="0" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Disable</button>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>

                  
                </tbody>
            </table>
        </div>
    </div>
<?php $obj->start_script(); ?>
<script src="assets/libs/jquery-tabledit/jquery.tabledit.min.js"></script>

    <script>
        $(document).ready(function () {
    
            $('#example').DataTable({
                        pageLength: 10,
                        lengthMenu: [ [10, 25, 100, 500], [10, 25, 100, 500] ],
                        responsive: true
                    });

            $('#example').on('click', 'tbody tr td button#secretCangeStatus', function (e) {
                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).html();
                $.ajax({
                    type: 'get',
                    url: './pages/mikrotik/connect_ajax.php',
                    data: {name: secretName, state: status},
                    success: function (result) {

                    }
                });
                if (status == 'Enable') {
                    $(this).html('Disable');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-danger');
                } else {
                    $(this).html('Enable');
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-success');
                }
            });
        });
    </script>
<?php $obj->end_script(); ?>
