$(function () {
    pages.permission.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    permission: {
        init: function () {

            var actionArray = ["view", "add", "edit", "delete", "all"];
            for (var i = 0; i < actionArray.length; i++) {
                var action = actionArray[i];
                var chkActionItems = $("input[action=" + action + "]");
                var countAction = 0;
                chkActionItems.each(function () {
                    var checked = $(this).is(':checked');
                    if (checked == true) {
                        countAction++;
                    }
                });
                if (countAction == chkActionItems.length) {
                    $("input[rel=" + action + "]").prop('checked', true);
                } else {
                    $("input[rel=" + action + "]").prop('checked', false);
                }
                pages.permission.setupCheckbox();
            }
            $(".checkAllController").change(function () {
                var checked = $(this).is(':checked');
                var value = $(this).attr("rel");
                if (checked == true) {
                    $("input[controller=" + value + "]").prop('checked', true);
                } else {
                    $("input[controller=" + value + "]").prop('checked', false);
                }
                $("input[controller=" + value + "]").change();
                var action = $(this).attr("action");
                var chkActionItems = $("input[action=" + action + "]");
                var countAction = 0;
                chkActionItems.each(function () {
                    var checked = $(this).is(':checked');
                    if (checked == true) {
                        countAction++;
                    }
                });
                if (countAction == chkActionItems.length) {
                    $("input[rel=" + action + "]").prop('checked', true);
                } else {
                    $("input[rel=" + action + "]").prop('checked', false);
                }
                pages.permission.setupCheckbox();
            });
            $(".checkAllAction").change(function () {
                var checked = $(this).is(':checked');
                var action = $(this).attr('rel');
                if (checked == true) {
                    $("input[action=" + action + "]").prop('checked', true);
                } else {
                    $("input[action=" + action + "]").prop('checked', false);
                }
                $("input[action=" + action + "]").change();
                pages.permission.setupCheckbox();
            });
            $(".chkItem").change(function () {
                var controller = $(this).attr("controller");
                var chkItems = $("input[controller=" + controller + "]");
                var count = 0;
                chkItems.each(function () {
                    var checked = $(this).is(':checked');
                    if (checked == true) {
                        count++;
                    }
                });
                if (count == chkItems.length) {
                    $("input[rel=" + controller + "]").prop('checked', true);
                } else {
                    $("input[rel=" + controller + "]").prop('checked', false);
                }
                var action = $(this).attr("action");
                var chkActionItems = $("input[action=" + action + "]");
                var countAction = 0;
                chkActionItems.each(function () {
                    var checked = $(this).is(':checked');
                    if (checked == true) {
                        countAction++;
                    }
                });
                if (countAction == chkActionItems.length) {
                    $("input[rel=" + action + "]").prop('checked', true);
                } else {
                    $("input[rel=" + action + "]").prop('checked', false);
                }
                var allCheckItems = $(".chkItem");
                var countAll = 0;
                allCheckItems.each(function () {
                    var checked = $(this).is(':checked');
                    if (checked == false) {
                        $("input[rel=all]").prop('checked', false);
                        return false;
                    } else {
                        countAll++;
                    }
                });
                if (countAll == allCheckItems.length) {
                    $("input[rel=all]").prop('checked', true);
                }
                pages.permission.setupCheckbox();
            });

            var aoColumns = [
                {"data": "role_id"},
                {"data": "role_name"},
                {"data": "status"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
                {
                    "render": function (data, type, row) {
                        var label = '';
                        if (row["status"] == 1) {
                            label = '<span class="label label-success">Active</span>';
                        } else if (row["status"] == -1) {
                            label = '<span class="label label-default">Inactive</span>';
                        }
                        return label;
                    },
                    orderable: true,
                    targets: 2
                },
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        if( row['edit_permission'] == true ){
                            action += '<li> <a href="/admin/permission/add?id=' + row.role_id + '" > <i class="icon-blog"></i> ' + translate('edit') + '</a > </li>';
                        }
                        if ( row['delete_permission'] == true) {
                            action += '<li> <a onclick="pages.permission.delete('+ row.role_id +')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';
                        }
                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 3,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#permission_list_dbt", "/admin/permission/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
            
            
            // jGrowl plugin
            // ------------------------------
            // Defaults override - hide "close all" button
            $.jGrowl.defaults.closer = false;
            
            var auoptions = {
                rules: {
                    p_name: {
                        required: true
                    }
                },
                messages: {
                    p_name: {
                        required: $("#p_name").attr('data-msg')
                    }
                }
            };
            pages.validation.setupValidation("#add_edit_permission_frm", auoptions);
            
            $(document).on('click', '#create-permission-btn', {}, function ( ) {
                if ( pages.validation.validator['#add_edit_permission_frm'].form() == false ) {
                    return false;
                }
                $("#add_edit_permission_frm").submit();
            });
            
            pages.permission.setupCheckbox();
        },
        //
        setupCheckbox : function(){
            // Primary
            $(".control-primary").uniform({
                radioClass: 'choice',
                wrapperClass: 'border-primary-600 text-primary-800'
            });
        },
        /**
         * 
         * @param {type} id
         * @returns {undefined}
         */
        delete: function (id) {
            $.ajax({
                'url': '/admin/permission/delete',
                'type': 'GET',
                'data': {id: id},
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.Code > 0) {
                        if ($("#permission_list_dbt").length > 0) {
                            var pTbl = $("#permission_list_dbt").DataTable();
                            pTbl.draw();
                        }
                        $.jGrowl(data.Message, {
                            theme: 'alert-styled-left bg-success'
                        });
                    } else {
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