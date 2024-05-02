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

                $(document).on('click', '.remove_image_2_botton', function() {
                    var count_image_2_botton = $('.image_2_botton_dm').length;
                    if(count_image_2_botton <101){
                        $("#add_image_2_botton").prop("disabled", false);
                    }
                    var index = $('.list-delete-image-2').attr('data-index');
                    var val = $(this).attr('data-remove-image-2');
                    var url = $(this).attr('data-url');
                    $('#delete_image_2_'+index).append('<input type="hidden" name="url_image_2_delete[]" value="'+val+'">'+'<input type="hidden" name="url_image_2_botton_delete[]" value="'+url+'">');
                    $(this).closest('.image_2_botton').remove();
                });

                $(document).on('click', '.remove_input_image_2_botton', function() {
                    var count_image_2_botton = $('.image_2_botton_dm').length;
                    if(count_image_2_botton <101){
                        $("#add_image_2_botton").prop("disabled", false);
                    }
                    $(this).parents('.input_image_2_botton').remove();
                });

                $(document).on('click', '.remove_image_3_botton', function() {
                    var count_image_3_botton = $('.image_3_botton_dm').length;
                    if(count_image_3_botton <101){
                        $("#add_image_3_botton").prop("disabled", false);
                    }
                    var index = $('.list-delete-image-3').attr('data-index');
                    var val = $(this).attr('data-remove-image-3');
                    var url = $(this).attr('data-url');
                    $('#delete_image_3_'+index).append('<input type="hidden" name="url_image_3_delete[]" value="'+val+'">'+'<input type="hidden" name="url_image_3_botton_delete[]" value="'+url+'">');
                    $(this).closest('.image_3_botton').remove();
                });

                $(document).on('click', '.remove_input_image_3_botton', function() {
                    var count_image_3_botton = $('.image_3_botton_dm').length;
                    if(count_image_3_botton <101){
                        $("#add_image_3_botton").prop("disabled", false);
                    }
                    $(this).parents('.input_image_3_botton').remove();
                });

                $(document).on('click', '.remove_image_12_botton', function() {
                    var count_image_12_botton = $('.image_12_botton_dm').length;
                    if(count_image_12_botton <101){
                        $("#add_image_12_botton").prop("disabled", false);
                    }
                    var index = $('.list-delete-image-12').attr('data-index');
                    var val = $(this).attr('data-remove-image-12');
                    var url = $(this).attr('data-url');
                    $('#delete_image_12_'+index).append('<input type="hidden" name="url_image_12_delete[]" value="'+val+'">'+'<input type="hidden" name="url_image_12_botton_delete[]" value="'+url+'">');
                    $(this).closest('.image_12_botton').remove();
                });

                $(document).on('click', '.remove_input_image_12_botton', function() {
                    var count_image_12_botton = $('.image_12_botton_dm').length;
                    if(count_image_12_botton <101){
                        $("#add_image_12_botton").prop("disabled", false);
                    }
                    $(this).parents('.input_image_12_botton').remove();
                });

                $(document).on('change', '#image_2_botton, #image_3_botton, #image_12_botton', function() {
                    checkFileSizes(this);
                });

           
                var countValue2 = $("#add_image_2_botton").find("span").attr("attr-count");
                var index2 = countValue2;
                $("#add_image_2_botton").click(function () {
                    var html = '';
                    html += 	'<div class="input_image_2_botton">';
                    html += 	'<div class="form-group">';
                    html += 	'<label class="control-label col-lg-2">Banner</label>';
                    html += 	'<div class="col-lg-3">';
                    html += 	    '<input type="file" class="file-styled form-control image_2_botton_dm " name="image_2_botton['+index2+']" accept="image/*"/>';
                    html += 	'</div>';
                    html += 	'<label class="control-label col-lg-1">URL</label>';
                    html += 	'<div class="col-lg-3">';
                    html += 	    '<input type="text" class="file-styled form-control url_image_2_botton " name="url_image_2_botton['+index2+']"/>';
                    html += 	'</div>';
                    html += 	'<div class="col-lg-2">';
                    html += 		'<button type="button"  class="btn btn-alert remove_input_image_2_botton" style="margin-right: 11px;">x</button>';
                    html += 	'</div>';
                    html += 	'</div>';
                    html += 	'</div>';
                    $('#new_image_2_botton').append(html);
                    index2++;
                });
            
                var countValue3 = $("#add_image_3_botton").find("span").attr("attr-count");
                var index3 = countValue3;
                $("#add_image_3_botton").click(function () {
                    var html = '';
                    html += 	'<div class="input_image_3_botton">';
                    html += 	'<div class="form-group">';
                    html += 	'<label class="control-label col-lg-2">Banner</label>';
                    html += 	'<div class="col-lg-3">';
                    html += 	    '<input type="file" class="file-styled form-control image_3_botton_dm " name="image_3_botton['+index3+']" accept="image/*"/>';
                    html += 	'</div>';
                    html += 	'<label class="control-label col-lg-1">URL</label>';
                    html += 	'<div class="col-lg-3">';
                    html += 	    '<input type="text" class="file-styled form-control url_image_3_botton " name="url_image_3_botton['+index3+']"/>';
                    html += 	'</div>';
                    html += 	'<div class="col-lg-2">';
                    html += 		'<button type="button"  class="btn btn-alert remove_input_image_3_botton" style="margin-right: 11px;">x</button>';
                    html += 	'</div>';
                    html += 	'</div>';
                    html += 	'</div>';
                    $('#new_image_3_botton').append(html);
                    index3++;
                });
                var countValue12 = $("#add_image_12_botton").find("span").attr("attr-count");
                var index12 = countValue12;
                $("#add_image_12_botton").click(function () {
                    var html = '';
                    html += 	'<div class="input_image_12_botton" >';
                    html += 	'<div class="form-group">';
                    html += 	'<label class="control-label col-lg-2">Banner</label>';
                    html += 	'<div class="col-lg-3">';
                    html += 	    '<input type="file" class="file-styled form-control image_12_botton_dm " name="image_12_botton['+index12+']" accept="image/*"/>';
                    html += 	'</div>';
                    html += 	'<label class="control-label col-lg-1">URL</label>';
                    html += 	'<div class="col-lg-3">';
                    html += 	    '<input type="text" class="file-styled form-control url_image_12_botton " name="url_image_12_botton['+index12+']"/>';
                    html += 	'</div>';
                    html += 	'<div class="col-lg-2">';
                    html += 		'<button type="button"  class="btn btn-alert remove_input_image_12_botton" style="margin-right: 11px;">x</button>';
                    html += 	'</div>';
                    html += 	'</div>';
                    html += 	'</div>';
                    $('#new_image_12_botton').append(html);
                    index12++;
                });
                $(document).ready(function() {
                    // image_2_botton
                    var count_image_2_botton = $('.image_2_botton_dm').length;
                    if(count_image_2_botton == 100){
                        $("#add_image_2_botton").prop("disabled", true);
                    }
                    $("#add_image_2_botton").click(function() {
                        var count_image_2_botton = $('.image_2_botton_dm').length;
                        if(count_image_2_botton == 100){
                            $("#add_image_2_botton").prop("disabled", true);
                        }
                    });
                    //image_3_botton
                    var count_image_3_botton = $('.image_3_botton_dm').length;
                    if(count_image_3_botton == 100){
                        $("#add_image_3_botton").prop("disabled", true);
                    }
                    $("#add_image_3_botton").click(function() {
                        var count_image_3_botton = $('.image_3_botton_dm').length;
                        if(count_image_3_botton == 100){
                            $("#add_image_3_botton").prop("disabled", true);
                        }
                    });

                    //image_12_botton
                    var count_image_12_botton = $('.image_12_botton_dm').length;
                    if(count_image_12_botton == 100){
                        $("#add_image_12_botton").prop("disabled", true);
                    }
                    $("#add_image_12_botton").click(function() {
                        var count_image_12_botton = $('.image_12_botton_dm').length;
                        if(count_image_12_botton == 100){
                            $("#add_image_12_botton").prop("disabled", true);
                        }
                    });
                });
                $(document).on('change', '.image_2_botton_dm', function() {
                    checkFileSizes(this);
                });
                $(document).on('change', '.image_3_botton_dm', function() {
                    checkFileSizes(this);
                });
                $(document).on('change', '.image_12_botton_dm', function() {
                    checkFileSizes(this);
                });

                function checkFileSizes(input) {
                    const files = input.files;
                    const maxSize = 324288;
                    let invalidFiles = [];
            
                    for (let i = 0; i < files.length; i++) {
                        const fileSize = files[i].size;
                        if (fileSize > maxSize) {
                            invalidFiles.push(files[i].name); 
                        }
                    }
            
                    if (invalidFiles.length > 0) {
                        alert("Các tệp tin sau có kích thước vượt quá 300kb:\n" + invalidFiles.join("\n"));
                        $(input).val(''); 
                    }
                }
                
                // $(document).on('click', '.remove_image_2_botton', function() {
                //     $(this).parents('.image_2_botton').remove();
                // });
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