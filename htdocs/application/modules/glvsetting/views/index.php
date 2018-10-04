<!-- MAIN CONTENT -->
<div id="content">
    <div class="row"><!-- col -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title"><!-- PAGE HEADER -->
                GLV Settings
            </h1>
        </div><!-- end col -->		
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <section id="widget-grid" class="">
                <div class="jarviswidget jarviswidget-sortable borderorrange" id="wid-id-01" data-widget-colorbutton="false" data-widget-editbutton="false" role="widget">
                    <header role="heading">
                    
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>  
                    </header>
                    <div role="content">
                        <!-- widget content -->
                        <div class="widget-body">                           
                           <div class="row form-group" >
                                <label class="col-md-1 control-label" style="padding-top: 5px;font-weight: bold;" for="txtDeptId">Upload size:</label>
                                <div class="col-md-3">
                                   <div class="col-md-10">
                                       <input id="txtUploadSize" class="form-control" placeholder="Upload Size" type="number" name="uploadSize" min="1" max="100" step="1" value="<?php echo $upload_setting['ValueSize'];?>">
                                                                                
                                            <span id ="error_upload" style="color: red"></span>
                                            <span id ="success_upload" style="color: blue"></span>
                                   </div>         
                                     <div class="col-md-1">
                                        <label style="padding-top: 5px;">MB</label>
                                    </div> 
                                </div>   
                                       
                                <div class="col-md-2">
                                    <button class="btn3d btn-blue btn-c" type="button" id="btnSave">
                                        Save
                                    </button>
                                </div>                               
                            </div>
                       </div>
                       <!-- end widget content -->

                   </div>
               </div>
           </section>
       </div>
   </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $("#txtUploadSize").on("keypress", function(evt) {
        var keycode = evt.charCode || evt.keyCode;
        if (keycode == 46) {
          return false;
        }
      });
    $("#txtUploadSize").keypress(function (e) {  
      
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }

        // Ensure that it is a number and stop the keypress
        if ( ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) || (this.value.length == 0 && e.which == 48 ) ) {
            e.preventDefault();
        }


    });

        $("#btnSave").click(function() {
          var size = parseInt($("#txtUploadSize").val());
          if(size<=0 || $("#txtUploadSize").val() ==''){
            $("#success_upload").text('');
            $("#txtUploadSize").val();
            $("#error_upload").text('Error! Only numeric data is accepted.');
          } else if(size>100){
            $("#success_upload").text('');
            $("#error_upload").text('Opp! File size which is greater than 100MB is not allowed.');
          }else{
            $("#txtUploadSize").val(size);
            var form_data = {
                  valueSize:size
                }
            $.ajax({
                  url: base_url + 'glvsetting/save',
                  async: false,
                  method: 'POST',
                  data: form_data,
                  success: function (data) {
                    $("#success_upload").text('Update success.');
                    $("#error_upload").text('');
                  },
                  error: function (xhr) {
                    $("#success_upload").text('');
                    alert("Error");
                  }
                });
          }
            
        });
});
</script>
    
