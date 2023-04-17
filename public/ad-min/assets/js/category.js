$(function () {
    pages.category.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    category: {
        init: function () {
            var me = this;
            pages.common.setupCheckbox();
            pages.common.setupDatePicker();
            $('.select-search').select2();
            $('[data-popup="lightbox"]').fancybox({});
            if (currController == 'category' && currAction == 'index') {
                me.initDatatable();
                $(document).on('click', '.seach-form', {}, function ( ) {
                    pages.common.executeSearchForm('searchCategory', 'categoryTable');
                });
            }
            if (currController == 'category' && currAction == 'detail') {
                me.initValidation();
                $.each($('.rich-editor'),function(){
        			var id = $(this).attr('id');
        			var h = $(this).attr('data-height');
        			 CKEDITOR.replace( id, {
          		       height: h,
          		       width: '100%',
   					   'image_previewText': '  ',
   					   'allowedContent': true,
   					   'enterMode' : CKEDITOR.ENTER_BR,
   					   
   					 filebrowserBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=files',
   					 filebrowserImageBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=images',
   					 filebrowserUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=files',
   					 filebrowserImageUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=images',
   					 filebrowserFlashUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=flash',
          		    });
        		});
                $(document).on('click', '.submit-btn', {}, function ( ) {
                    if (pages.validation.validator['#categoryDetailForm'].form() == false) {
                        return false;
                    }
                    $(".submit-btn").submit();
                });
                $(document).on('change', '#url_slug', {}, function ( ) {
                    var value = $(this).val();
                    value = pages.common.string_to_slug(value);
                    $(this).val(value);
                });

                $(document).on("change", "#parent_category", {}, function () {
                    var v = $("#parent_category option:selected").attr("data-level");
                    var level = 0;
                    level = parseInt(v) + 1;
                    $("#level_category_display").val(level);
                    $("#level_category").val(level);
                });
            }
        },
        initValidation: function () {
            var loptions = {
                rules: {
                    name: {
                        required: true
                    },
                    url_slug: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: $("#name").attr('data-msg')
                    },
                    url_slug: {
                        required: $("#url_slug").attr('data-msg')
                    },
                }
            };
            pages.validation.setupValidation("#categoryDetailForm", loptions);

        },
        initDatatable: function () {
            var aoColumns = [
                {"data": "id"},
                {"data": "name"},
                {"data": "url_slug"},
                {"data": "image"},
                {"data": "url_menu"},
                {"data": "status"},
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
                        if (pages.core.isDefined(data)) {
                            return row['url_slug'];
                        } else {
                            return '';
                        }
                    },
                    "targets": 2,
                    "orderable": true,
                    "data": "url_slug"
                },
                {
                    "render": function (data, type, row) {
                        var img = '';
                        if (pages.core.isDefined(row['image']) && row['image'] != null) {
                            var img = '<a href="/upload/images' + row['image'] + '" data-popup="lightbox">'
                                    + '<img src="/upload/images' + row['image'] + '" alt="" class="img-rounded img-preview">'
                                    + '</a>';
                        }
                        return img;
                    },
                    "targets": 3,
                    "orderable": true,
                    "data": "image"
                },
                {
                    "render": function (data, type, row) {
                        if (pages.core.isDefined(data)) {
                            return row['menu_name'];
                        } else {
                            return '-';
                        }
                    },
                    "targets": 4,
                    "orderable": true,
                    "data": "url_menu"
                },
                {
                    "render": function (data, type, row) {
                        var label = '';
                        if (row["status"] == 1) {
                            label = '<span class="label label-success">' + translate('active') + '</span>';
                        } else if (row["status"] == -1) {
                            label = '<span class="label label-default">' + translate('disabled') + '</span>';
                        }
                        return label;
                    },
                    orderable: true,
                    targets: 5
                },
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/category/detail/id/' + row.id + '" > <i class="icon-pencil3"></i> ' + translate('edit') + '</a > </li>';
                        action += '<li> <a onclick="pages.category.deleteCategory(' + row.id + ')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';
                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 6,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#categoryTable", "/admin/category/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
        deleteCategory: function (id) {
            bootbox.confirm('Bạn có muốn xóa danh mục này?', function (result) {
                if (result) {
                    $.ajax({
                        'url': '/admin/category/delete',
                        'type': 'GET',
                        'data': {id: id},
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                if ($("#categoryTable").length > 0) {
                                    bootbox.alert('Xóa thành công');
                                    var t = $("#categoryTable").DataTable();
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