// Edit user event
function edit_user(user_id) {
    $('#ModalEditUser').removeData();
    $("#loadingModal").modal('show');
    $('#ModalEditUser').load(base_url + 'usersmanagement/edit/'+user_id);
    setTimeout(function () {
        $('#ModalEditUser').modal('show');
        $("#loadingModal").modal('hide');
    }, 3000);
}

// Add user event
function add_user() {
    $('#ModalEditUser').removeData();
    $("#loadingModal").modal('show');
    $('#ModalEditUser').load(base_url + 'usersmanagement/add');
    setTimeout(function () {
        $('#ModalEditUser').modal('show');
        $("#loadingModal").modal('hide');
    }, 3000);
}
// Delete user event
function delete_user(id,user_id) {
    var result = confirm("Do you want to delete this user?");
    if (result) {
        var form_data = {
            id: id,
            user_id: user_id
        }
        $("#loadingModal").modal('show');
        $.ajax({
            type: "POST",
            url: base_url + "usersmanagement/delete",
            data: form_data,
            success: function(data){
                $("#loadingModal").modal('hide');
                $('#user_content_table').load('usersmanagement/list_users');
            }
        });
    }
}