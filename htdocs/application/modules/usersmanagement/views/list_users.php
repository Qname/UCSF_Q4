<script type="text/javascript">
    $(document).ready(function() {
       $('#dt_users').DataTable( {
          "autoWidth": false,
          "bFilter" : false,
          "infoEmpty": "No records available",
	    	"processing": true, //Feature control the processing indicator.
	        //"serverSide": true, //Feature control DataTables' server-side processing mode.
	        "fixedHeader": true,
	        "scrollX":  '100%',
	        "scrollY":  '100vh',
	        "scrollCollapse": true,
            "sDom": 'Rfrtlip',
            //"paging": false,
            "ordering": false,
            "bInfo": false,
            columnDefs: [ 
            {	"targets": 0,"visible": false,"searchable": false},
            {	"class": "col170","targets": [1,2] },
            {   "class": "col100","targets": [3,4] },
            {   "class": "col170","targets": [5,6] },
            {   "class": "col100","targets": [7,8] },
            {   "class": "col110","targets": [9] }
            ]
        });
   });
</script>
<table id="dt_users" class="table table-striped table-bordered table-hover dataTable no-footer" width="2500px;">
    <thead>
        <tr>
            <th data-hide="phone">uniqueid</th>
            <th data-class="expand">UCSF ID</th>
            <th data-hide="phone">Account Name</th>
            <th data-hide="phone,tablet">First Name</th>
            <th data-hide="phone,tablet">Last Name</th>
            <th data-hide="phone,tablet">Email</th>
            <th data-hide="phone,tablet">Department Name</th>
            <th data-hide="phone,tablet">Role</th>
            <th data-hide="phone,tablet">Created Date</th>
            <th data-hide="phone,tablet">Actions</th><!--count-->
        </tr>
    </thead>
    <tbody>
    	<?php
      foreach($users as $user) {
       ?>
       <tr>
        <td><?php echo $user->id; ?></td>
        <td><?php echo $user->user_id; ?></td>
        <td><?php echo $user->user_name; ?></td>
        <td><?php echo $user->nameFirst; ?></td>
        <td><?php echo $user->nameLast; ?></td>
        <td><?php echo $user->email; ?></td>
        <td><?php echo $user->departmentname; ?></td>
        <td><?php echo $user->authorized_role; ?></td>
        <td><?php echo date("m/d/Y",strtotime($user->createdate)); ?></td>
        <td style="width:130px;"><input style="float:left; margin-right:16px;" type="button" onclick="return edit_user(<?php echo $user->id;?>);" value="Edit"/><input <?php if(isset($this->session->userdata['userid'])){ if ($this->session->userdata['userid'] == $user->user_id) {echo 'class="dispay-none"';} }?>  style="float:left;" type="button" onclick="return delete_user(<?php echo $user->id;?>,'<?php echo $user->user_id;?>')" value="Delete"/></td>
    </tr>
    <?php
}
?>

</tbody>
</table>
