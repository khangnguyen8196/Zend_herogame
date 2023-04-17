$(function () {
    pages.user.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    user: {
        init: function () {
            var aoColumns = [
                {"data": "user_id"},
                {"data": "user_name"},
                {"data": "first_name"},
                {"data": "last_name"},
                {"data": "email"},
                {"data": "role_id"},
                {"data": "status"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
                {
                    "render": function (data, type, row) {
                        var roles =  $.parseJSON( roleList );
                        var dropdownMenu = '';
                        $.each( roles, function ( key, value ){
                            if( parseInt(row.role_id) == parseInt(value['role_id'])){
                                return true;
                            }
                            dropdownMenu += '<li><a onclick="pages.user.changeUserInfo('+  row.user_id +','+ "''" +','+value['role_id']+')">'+value['role_name']+'</a></li>';
                        });
                        
                        var action = '<ul class="icons-list">'+
                            '<li>'+row["role_name"]+'</li>';
                            if( row['edit_permission'] == true ){
                            action += '<li class="dropdown">'+
                               ' <a href="#" class="dropdown-toggle" data-toggle="dropdown">'+
                                    '<i class="icon-cog7"></i>'+
                                    '<span class="caret"></span>'+
                                '</a>'+
                                '<ul class="dropdown-menu">'+ dropdownMenu+
                                '</ul>'+
                            '</li>';
                            }
			action += '</ul>';
                        return action;
                    },
                    orderable: true,
                    targets: 5
                },
                {
                    "render": function (data, type, row) {
                        var label = '';
                        if( row["status"] == 1){
                            label = '<span class="label label-success">Active</span>';
                        } else if( row["status"] == -1 ){
                             label = '<span class="label label-default">Inactive</span>';
                        }
                        return label;
                    },
                    orderable: true,
                    targets: 6
                },
                {
                    "render": function (data, type, row) {
                        var action = '';
                        if( row['edit_permission'] == true || row['delete_permission'] == true){
                         action += '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        }
                        if( row['edit_permission'] == true ){
                            if (row.status == 1) {
                                action += '<li> <a onclick="pages.user.changeUserInfo('+  row.user_id +','+ pages.constant.STATUS_IN_ACTIVE+','+"''" +')" > <i class="icon-minus-circle2"></i>'+ translate('disabled')+'</a > </li>';
                            } else if(row.status == -1){
                                action += '<li> <a onclick="pages.user.changeUserInfo('+  row.user_id +','+ pages.constant.STATUS_ACTIVE+','+ "''" +')" > <i class="icon-checkmark4"></i>'+ translate('active')+'</a > </li>';
                            }
                        }
                        if ( row['delete_permission'] == true) {
                            action += '<li> <a onclick="pages.user.deleteUser('+  row.user_id +')" > <i class="icon-bin"></i> '+ translate('delete')+'</a > </li></ul></li></ul>';
                        }
                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 7,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#user_list_dbt", "/admin/user/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
            // Default initialization
            $('.select-role').select2({
                minimumResultsForSearch: "-1"
            });
            // jGrowl plugin
            // ------------------------------
            // Defaults override - hide "close all" button
            $.jGrowl.defaults.closer = false;
            
            var auoptions = {
                rules: {
                    username: {
                        required: true
                    },
                    first_name: {
                        required: true
                    },
                    last_name: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    repeat_password: {
                        required: true,
                        equalTo: "#password"
                    },
                    email: {
                        required: true,
                        email:  true
                    },
                    repeat_email: {
                        required: true,
                        email:  true,
                        equalTo: "#email"
                    }
                },
                messages: {
                    username: {
                        required: $("#username").attr('data-msg')
                    },
                    first_name: {
                        required: $("#first_name").attr('data-msg')
                    },
                    last_name: {
                        required: $("#last_name").attr('data-msg')
                    },
                    password: {
                        required: $("#password").attr('data-msg')
                    },
                    repeat_password: {
                        required: $("#repeat_password").attr('data-msg'),
                        equalTo: $("#repeat_password").attr('data-miss-match-msg')
                    },
                    email: {
                        required: $("#email").attr('data-msg'),
                        email:  $("#email").attr('data-error-email')
                    },
                    repeat_email: {
                        required: $("#repeat_email").attr('data-msg'),
                        email:  $("#repeat_email").attr('data-error-email'),
                        equalTo: $("#repeat_email").attr('data-miss-match-msg')
                    }
                }
            };
            pages.validation.setupValidation("#add_edit_user_frm", auoptions);
            
            $(document).on('click', '#add_edit_user_frm #add-update-user-btns', {}, function ( ) {
                if ( pages.validation.validator['#add_edit_user_frm'].form() == false ) {
                    return false;
                }
                $("#add_edit_user_frm").submit();
            });
            
        },
        
        //
        deleteUser: function (userId) {
            $.ajax({
                'url': '/admin/user/delete',
                'type': 'GET',
                'data': {userId: userId},
                beforeSend: function () {

                },
                success: function (data) {
                    if( data.Code > 0 ){
                        if ($("#user_list_dbt").length > 0) {
                            var userTbl = $("#user_list_dbt").DataTable();
                            userTbl.draw();
                        }
                        $.jGrowl(data.Message, {
                            theme: 'alert-styled-left bg-success'
                        });
                    }else{
                        $.jGrowl(data.Message, {
                            header: 'Left icon',
                            theme: 'alert-styled-left bg-danger'
                        });
                    }
                },
                error: function (data) {

                }
            });
        },
        //
        changeUserInfo : function( userId , status, role ){
            $.ajax({
                'url': '/admin/user/change-user-info',
                'type': 'GET',
                'data': {userId: userId, status: status, role: role},
                beforeSend: function () {

                },
                success: function (data) {
                    if( data.Code > 0 ){
                        if ($("#user_list_dbt").length > 0) {
                            var userTbl = $("#user_list_dbt").DataTable();
                            userTbl.draw();
                        }
                        $.jGrowl(data.Message, {
                            theme: 'alert-styled-left bg-success'
                        });
                    }else{
                        $.jGrowl(data.Message, {
                            header: 'Left icon',
                            theme: 'alert-styled-left bg-danger'
                        });
                    }
                },
                error: function (data) {

                }
            });
        }
    }
});