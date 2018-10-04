<div class="modal-dialog" id="edit_user_content">
	<div class="modal-content">
		<div class="modal-header modal-header-custom">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
			<h4 class="modal-title modal-title-main" style="font-weight:bold;">Add User</h4>
		</div>
		<div class="jsError"></div>
		<?php //echo validation_errors(); ?>
		<?php echo form_open('/usersmanagement/save',array('class' => 'edit_user_form')); ?>
		<!--<form name="user" method="post" action="/usersmanagement/save" id="user">-->
			<div class="modal-body row">
				<div class="col-lg-12">
					<fieldset style="margin-bottom:20px;">
						<legend>User Info</legend> 
						<div class="textfield">
							<label class="control-label" style="width:30%;" for="user_id">UCSF ID: <span style="color:red;">*</span></label>
							<input type="text" name="user_id" value="" style="width:60%;" id="user_id" class="required">
							<div class="error-user-form" id="error_user_id"></div>
						</div>

						<div class="textfield padding-top-10">
							<label class="control-label" style="width:30%;" for="user_name">Account Name <span style="color:red;">*</span></label>
							<input type="text" name="user_name" value="" style="width:60%;" id="user_name" class="required">
							<div class="error-user-form" id="error_user_name"></div>
						</div>

						<div class="textfield padding-top-10">
							<label class="control-label" style="width:30%;" for="nameFirst">First Name</label>
							<input type="text" name="nameFirst" value="" style="width:60%;" id="nameFirst" class="">
						</div>

						<div class="textfield padding-top-10">
							<label class="control-label" style="width:30%;" for="nameLast">Last Name</label>
							<input type="text" name="nameLast" value="" style="width:60%;" id="nameLast" class="">
						</div>

						<div class="textfield padding-top-10">
							<label class="control-label" style="width:30%;" for="email">Email</label>
							<input type="text" name="email" value="" style="width:60%;" id="email" class="email">
							<div class="error-user-form" id="error_user_email"></div>
						</div>

						<div class="textfield padding-top-10">
							<label class="control-label" style="width:30%;" for="departmentname">Department Name</label>
							<input type="text" name="departmentname" value="" style="width:60%;" id="departmentname" class="">
						</div>
						
					</fieldset>
					<div style="margin-bottom:20px;">
						<fieldset>
							<legend>Security</legend>
							<div class="textfield">
								<label id="lblAuthorized" class="control-label" style="width:30%; float:left;" for="authorized_role">Role <span style="color:red;">*</span></label>
								<div style="margin-left:30.4%;">
									<?php foreach ($roles as $role) { ?>
									<span>
										<input aria-labelledby="lblAuthorized" type="radio" name="authorized_role" value="<?php echo $role->role_name;?>"/><?php echo $role->role_name;?>
									</br>
								</span>
								<?php } ?>
							</div>
							
							<div class="error-user-form" id="error_authorized_role"></div>
							<div class="textfield padding-top-10" id="allowDeptDiv">
								<label class="control-label" style="width:30.4%; float:left;" for="allowDeptId">Allowed Dept IDs: </label>
								<input type="text" name="allowDeptId" title="Enter Dept IDs separated by comma." value="" style="width:60%;" id="allowDeptId" class="">
								
							</div>
							<div class="error-user-form" id="error_allowDeptId"></div>
						</div>
					</fieldset>			            
				</div>
				<div class="" style="text-align: left;position; relative;border-top:1px solid #e5e5e5;padding:20px;">
					<input type="button" style="float: right;" name="submit" id="submit_edit_user" value="Submit" class="btn">
					<input type="button" style="float: right;margin-right:10px;" name="cancel" id="cancel_edit_user" value="Cancel" class="btn">
				</div>
			</form>
		</div>
	</div>

	
</div>
</div>

<script type="text/javascript">
	
	
	$(document).ready(function(){
		$('#cancel_edit_user').on('click', function() {
			$('#ModalEditUser').modal('hide');
		});
		$('#allowDeptDiv').hide();	
		$("input[name=authorized_role]").click(function() {
			if($('input[name=authorized_role]:checked').val()=='Approver'){
				$('#allowDeptDiv').show();
				
			}else{
				$('#allowDeptDiv').hide();
				$("#error_allowDeptId").html("");
			}
		});

		$('#submit_edit_user').on('click', function() {
			$("#loadingModal").modal('show');
			$("#error_user_id").html("");
			$("#error_user_name").html("");
			$("#error_user_email").html("");
			$("#error_authorized_role").html("");
			$("#error_allowDeptId").html("");

			if($.trim($("#user_id").val()).length<1){
				$("#error_user_id").html("UCSF ID field is required.");
			}
			if($.trim($("#user_name").val()).length<1){
				$("#error_user_name").html("Account Name field is required.");
			}

			if($('input[name=authorized_role]:checked').val()=='Approver'){


				var count =0;
				var listDeptValue = $('#allowDeptId').val();
				var listDeptArray = $.trim(listDeptValue).split(',');
				var newDeptAllowValue ="";

	        // splice space data
	        for(var i = listDeptArray.length - 1; i >= 0; i--) {
	        	if($.trim(listDeptArray[i]).length==0) {
	        		listDeptArray.splice(i, 1);
	        	}
	        }

	          // update new value for allowDeptId txt
	          for (var i = 0; i < listDeptArray.length; i++) {
	          	newDeptAllowValue+=$.trim(listDeptArray[i])+",";
	          }
	          newDeptAllowValue=newDeptAllowValue.substring(0, newDeptAllowValue.length-1);
	          $('#allowDeptId').val(newDeptAllowValue);

	          var listDeptInvalid ="";
	          //check empty department list
	          if(listDeptArray.length<1){
	          	$.post(base_url + '/usersmanagement/save', $('form.edit_user_form').serialize(), function(data) {
	          		data = JSON.parse(data);			        
	          		$("#loadingModal").modal('hide');
	          		if (data != "success") {
	          			$("#error_user_id").html(data.user_id);
	          			$("#error_user_name").html(data.user_name);
	          			$("#error_user_email").html(data.email);
	          			$("#error_authorized_role").html(data.authorized_role);
	          		}
	          		else {
	          			$('#ModalEditUser').modal('hide');
	          			$('#user_content_table').load('usersmanagement/list_users');
	          		}
	          	});	
	          }else{

	          	

	         	// check valid department IDs
	         	for (var i = 0; i < listDeptArray.length; i++) {
	         		if($.trim(listDeptArray[i]).length!=6 ||parseInt(listDeptArray[i])<100000 ||parseInt(listDeptArray[i])==NaN ||
	         			(parseInt(listDeptArray[i])<300000 && parseInt(listDeptArray[i])>=200000) || $.trim(listDeptArray[i])=='------'
	         			|| parseInt(listDeptArray[i])==999999){
	         			count++;
	         		listDeptInvalid+= $.trim(listDeptArray[i])+",";
	         	}
	         }
	         if(count==0){
	         	listDeptArray = listDeptArray.sort(); 
					// Check Duplicate data department IDs
					for (var i = 0; i < listDeptArray.length - 1; i++) {
						if ($.trim(listDeptArray[i + 1]) == $.trim(listDeptArray[i])) {
							count++;
							listDeptInvalid+=$.trim(listDeptArray[i])+",";
						}
					}
					
					if(count==0){
						

								var form_data = {
									listDeptIds:listDeptArray,
									deptAmount:listDeptArray.length
								}
								$.ajax({
									url: base_url + 'usersmanagement/checkDeptAllowed',
									async: false,
									method: 'POST',
									data: form_data,
									success: function (data) {
										
										if(data==true){
											
											$.post(base_url + '/usersmanagement/save', $('form.edit_user_form').serialize(), function(data) {
												data = JSON.parse(data);			        
												$("#loadingModal").modal('hide');
												if (data != "success") {
													$("#error_user_id").html(data.user_id);
													$("#error_user_name").html(data.user_name);
													$("#error_user_email").html(data.email);
													$("#error_authorized_role").html(data.authorized_role);
												}
												else {
													$('#ModalEditUser').modal('hide');
													$('#user_content_table').load('usersmanagement/list_users');
												}
											});	
										}else{
											var listValidDpet = data.substring(0, data.length-1);
											
											for (var i = 0; i < listDeptArray.length; i++) {
												if(listValidDpet.indexOf($.trim(listDeptArray[i]))<0)
													listDeptInvalid+=$.trim(listDeptArray[i])+",";
											}
											listDeptInvalid=listDeptInvalid.substring(0, listDeptInvalid.length-1);
											$("#loadingModal").modal('hide');
											$("#error_allowDeptId").html("Invalid Dept IDs: " +listDeptInvalid);
										}
									},
									error: function (xhr) {
										alert("Error");
										$("#loadingModal").modal('hide');
									}
								});
							}else{
								listDeptInvalid=listDeptInvalid.substring(0, listDeptInvalid.length-1);
								var listDeptInvalidArray =listDeptInvalid.split(',');
									// Remove more Duplicate data in  listDeptInvalid
									for(var i = listDeptInvalidArray.length - 1; i >= 0; i--) {
										if($.trim(listDeptInvalidArray[i+1])==$.trim(listDeptInvalidArray[i])) {
											listDeptInvalidArray.splice(i, 1);
										}
									}
									
									$("#error_allowDeptId").html("Duplicate Dept IDs: " +listDeptInvalidArray);
									$("#loadingModal").modal('hide');
								}	

							}else{
								listDeptInvalid=listDeptInvalid.substring(0, listDeptInvalid.length-1);
								$("#loadingModal").modal('hide');
								$("#error_allowDeptId").html("Invalid Dept IDs: " +listDeptInvalid);
							}
							
						}
						
					}else{
						$('#allowDeptId').val('');
						$.post(base_url + '/usersmanagement/save', $('form.edit_user_form').serialize(), function(data) {
							data = JSON.parse(data);			        
							$("#loadingModal").modal('hide');
							if (data != "success") {
								$("#error_user_id").html(data.user_id);
								$("#error_user_name").html(data.user_name);
								$("#error_user_email").html(data.email);
								$("#error_authorized_role").html(data.authorized_role);
							}
							else {
								$('#ModalEditUser').modal('hide');
								$('#user_content_table').load('usersmanagement/list_users');
							}
						});	
					}
					

					
				});
});
</script>