$(function () {
    pages.promoCode.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    promoCode: {
        init: function () {
            var me = this;
            pages.common.setupCheckbox();
            pages.common.setupDatePicker();
            if (currController == 'promotion-code' && currAction == 'index') {
                me.initDatatable();
                $(document).on('click', '.seach-form', {}, function ( ) {
                    pages.common.executeSearchForm('searchPromoCode', 'promoCodeTable');
                });
            }
            if (currController == 'promoCode' && currAction == 'detail') {
                me.initValidation();
                $(document).on('click', '.submit-btn', {}, function ( ) {
                    if (pages.validation.validator['#promoCodeDetailForm'].form() == false) {
                        return false;
                    }
                    $(".submit-btn").submit();
                });
            }
        },
        initValidation: function () {
            var loptions = {
                rules: {
                    name: {
                        required: true
                    },
                    code: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: $("#name").attr('data-msg')
                    },
                    code: {
                        required: $("#code").attr('data-msg')
                    },
                }
            };
            pages.validation.setupValidation("#promoCodeDetailForm", loptions);

        },
        initDatatable: function () {
        	console.log('0000');
            var aoColumns = [
                {"data": "id"},
                {"data": "name"},
                {"data": "code"},
                {"data": "startdate"},
                {"data": "enddate"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/promotion-code/detail/id/' + row.id + '" > <i class="icon-pencil3"></i> Sửa</a > </li>';
                        action += '<li> <a onclick="pages.promoCode.deletePromoCode(' + row.id + ')" > <i class="icon-bin"></i> Xóa</a > </li></ul></li></ul>';
                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 5,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#promoCodeTable", "/admin/promotion-code/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
        deletePromoCode: function (id) {
            bootbox.confirm('Bạn có muốn xóa khuyến mãi này?', function (result) {
                if (result) {
                    $.ajax({
                        'url': '/admin/promotion-code/delete',
                        'type': 'GET',
                        'data': {id: id},
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                if ($("#promoCodeTable").length > 0) {
                                    bootbox.alert('Xóa thành công');
                                    var t = $("#promoCodeTable").DataTable();
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