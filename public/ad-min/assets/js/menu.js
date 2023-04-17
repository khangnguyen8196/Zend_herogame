$(function () {
    pages.menu.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    menu: {
        init: function () {
            var me = this;
            pages.common.setupCheckbox();
            pages.common.setupDatePicker();
            //setup fancybox
            $('[data-popup="lightbox"]').fancybox({});
            $('.select-search').select2();
            if (currController == 'menu' && currAction == 'index') {
                me.initDatatable();
                $(document).on('click', '.seach-form', {}, function ( ) {
                    pages.common.executeSearchForm('searchmenu', 'menuTable');
                });
            }
            if (currController == 'menu' && currAction == 'detail') {
                me.initValidation();
                $(document).on('click', '.submit-btn', {}, function ( ) {
                    if (pages.validation.validator['#menuDetailForm'].form() == false) {
                        return false;
                    }
                    $(".submit-btn").submit();
                });
                $(document).on("change", "#parent_menu", {}, function () {
                    var v = $("#parent_menu option:selected").attr("data-level");
                    var level = 0;
                    if (v != '') {
                        level = parseInt(v) + 1;
                    }
                    $("#level_menu_display").val(level);
                    $("#level").val(level);
                });

            }
            $(document).on('click', '.media-select-image', {}, function ( ) {
                var url = $(this).attr('data-src');
                var frontUrl = frontLink;
                $('#og_image').val(frontUrl.substring(0, frontUrl.length - 1) + url);
                $('.close-media-dialog').click();
            });
            $(document).on('click', '#select-media', {}, function ( ) {
                pages.menu.showMedia();
            });
        },
        initValidation: function () {
            var loptions = {
                rules: {
                    name: {
                        required: true
                    },
                    url: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: $("#name").attr('data-msg')
                    },
                    url: {
                        required: $("#url").attr('data-msg')
                    }
                }
            };
            pages.validation.setupValidation("#menuDetailForm", loptions);

        },
        initDatatable: function () {
            var aoColumns = [
                {"data": "id"},
                {"data": "name"},
                {"data": "url"},
                {"data": "level"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
                {
                    "render": function (data, type, row) {
                        return row["name"];
                    },
                    "orderable": true,
                    targets: 1
                },
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/menu/detail/id/' + row.id + '" > <i class="icon-pencil3"></i> ' + translate('edit') + '</a > </li>';
                        action += '<li> <a onclick="pages.menu.deletemenu(' + row.id + ')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';
                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 4,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#menuTable", "/admin/menu/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
        deletemenu: function (id) {
            bootbox.confirm('Bạn có muốn xóa menu này không', function (result) {
                if (result) {
                    $.ajax({
                        'url': '/admin/menu/delete',
                        'type': 'GET',
                        'data': {id: id},
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                if ($("#menuTable").length > 0) {
                                    bootbox.alert('Xóa thành công');
                                    var t = $("#menuTable").DataTable();
                                    t.draw();
                                }
                            } else {
                                bootbox.alert('Xóa thất bại');
                            }
                        },
                        error: function (data) {

                        }
                    });
                }
            });
        }
    }
});