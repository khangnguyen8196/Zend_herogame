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
                // image 2 botton
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
                // image 2 top
                $(document).on('click', '.remove_image_2_top', function() {
                    var count_image_2_top = $('.image_2_top_dm').length;
                    if(count_image_2_top <101){
                        $("#add_image_2_top").prop("disabled", false);
                    }
                    var index = $('.list-delete-image-2-top').attr('data-index');
                    var val = $(this).attr('data-remove-image-2-top');
                    var url = $(this).attr('data-url');
                    $('#delete_image_2_top'+index).append('<input type="hidden" name="url_image_2_delete_top[]" value="'+val+'">'+'<input type="hidden" name="url_image_2_top_delete[]" value="'+url+'">');
                    $(this).closest('.image_2_top').remove();
                });

                $(document).on('click', '.remove_input_image_2_top', function() {
                    var count_image_2_top = $('.image_2_top_dm').length;
                    if(count_image_2_top <101){
                        $("#add_image_2_top").prop("disabled", false);
                    }
                    $(this).parents('.input_image_2_top').remove();
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

                $(document).on('click', '.remove_image_slide_category', function() {
                    var count_image_slide_category = $('.image_slide_category_dm').length;
                    if(count_image_slide_category <101){
                        $("#add_image_slide_category").prop("disabled", false);
                    }
                    var index = $('.list-delete-image-slide-category').attr('data-index');
                    var val = $(this).attr('data-remove-image-slide-category');
                    var url = $(this).attr('data-url');
                    $('#delete_image_slide_category_'+index).append('<input type="hidden" name="url_image_slide_category_delete[]" value="'+val+'">'+'<input type="hidden" name="url_image_slide_category_botton_delete[]" value="'+url+'">');
                    $(this).closest('.image_slide_category').remove();
                });

                $(document).on('click', '.remove_input_image_slide_category', function() {
                    var count_image_slide_category = $('.image_slide_category_dm').length;
                    if(count_image_slide_category <101){
                        $("#add_image_slide_category").prop("disabled", false);
                    }
                    $(this).parents('.input_image_slide_category').remove();
                });

                me.handleRemoveImage('.remove_image_banner_ytb', 'data-remove-image-banner-ytb', '.delete-image-banner-ytb', 'url_image_banner_ytb_delete', '.banner-youtube');
                me.handleRemoveImage('.remove_image_banner_ytb_left', 'data-remove-image-banner-ytb-left', '.delete-image-banner-ytb-left', 'url_image_banner_ytb_delete_left', '.banner-youtube-left');
                me.handleRemoveImage('.remove_image_banner_ytb_mid', 'data-remove-image-banner-ytb-mid', '.delete-image-banner-ytb-mid', 'url_image_banner_ytb_delete_mid', '.banner-youtube-mid');
                me.handleRemoveImage('.remove_image_banner_ytb_right', 'data-remove-image-banner-ytb-right', '.delete-image-banner-ytb-right', 'url_image_banner_ytb_delete_right', '.banner-youtube-right');
                
                me.handleRemoveImage('.remove_image_banner_ytb_2', 'data-remove-image-banner-ytb-2', '.delete-image-banner-ytb-2', 'url_image_banner_ytb_delete_2', '.banner-youtube-2');
                me.handleRemoveImage('.remove_image_banner_ytb_left_2', 'data-remove-image-banner-ytb-left-2', '.delete-image-banner-ytb-left-2', 'url_image_banner_ytb_delete_left_2', '.banner-youtube-left-2');
                me.handleRemoveImage('.remove_image_banner_ytb_mid_2', 'data-remove-image-banner-ytb-mid-2', '.delete-image-banner-ytb-mid-2', 'url_image_banner_ytb_delete_mid_2', '.banner-youtube-mid-2');
                me.handleRemoveImage('.remove_image_banner_ytb_right_2', 'data-remove-image-banner-ytb-right-2', '.delete-image-banner-ytb-right-2', 'url_image_banner_ytb_delete_right_2', '.banner-youtube-right-2');

                me.setupAddImageButton("#add_image_2_top", "#new_image_2_top", "image_2_top", "image_2_top");
                me.setupAddImageButton("#add_image_2_botton", "#new_image_2_botton", "image_2_botton", "image_2_botton");
                me.setupAddImageButton("#add_image_3_botton", "#new_image_3_botton", "image_3_botton", "image_3_botton");
                me.setupAddImageButton("#add_image_12_botton", "#new_image_12_botton", "image_12_botton", "image_12_botton");

                me.setupAddImageButton("#add_image_slide_category", "#new_image_slide_category", "image_slide_category", "image_slide_category");

                me.setupImageLimit("#add_image_2_top", ".image_2_top_dm");
                me.setupImageLimit("#add_image_2_botton", ".image_2_botton_dm");
                me.setupImageLimit("#add_image_3_botton", ".image_3_botton_dm");
                me.setupImageLimit("#add_image_12_botton", ".image_12_botton_dm");

                me.setupImageLimit("#add_image_slide_category", ".image_slide_category_dm");

                // $(document).on('change', '.image_2_botton_dm, .image_2_top_dm, .image_3_botton_dm, .image_12_botton_dm, .image_slide_category_dm', function() {
                //     checkFileSizes(this);
                // });
                $(document).on('change', [
                    '.image_2_botton_dm',
                    '.image_2_top_dm',
                    '.image_3_botton_dm',
                    '.image_12_botton_dm',
                    '.image_slide_category_dm',
                    '#banner_ytb',
                    '#banner_ytb_L',
                    '#banner_ytb_M',
                    '#banner_ytb_R',
                    '#banner_ytb_2',
                    '#banner_ytb_L_2',
                    '#banner_ytb_M_2',
                    '#banner_ytb_R_2'
                ].join(', '), function() {
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
            }
            if( currController == 'category' && currAction == 'media'){
        		pages.common.setupMasonry();
        		$(document).on('click', '.choose-img', function() {
                   url = $(this).attr('data-src');
                   var functionNum = $("#CKEditorFuncNum").val();
                   window.opener.CKEDITOR.tools.callFunction(functionNum, url, '');
                   window.close();
                });
        	}
            $(document).on('click', '.select-media', {}, function ( ) {
                pages.category.showMedia();
            });
        
            $(document).on('click', '.media-select-image', {}, function ( ) {
                var url = $(this).attr('data-src');
                var frontUrl = frontLink;
                $('#og_image').val( frontUrl.substring(0, frontUrl.length-1) + url );
                $('.close-media-dialog').click();
            });
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
        showMedia: function () {
            $.ajax({
                url: '/admin/media/get-list-media',
                type: 'GET',
                data: {},
                beforeSend: function ( ) {
                },
                success: function (data) {
                    if (data.Code > 0) {
                        $('#modal_media').html(data.Data);
                        pages.common.setupMasonry();
                        $('#modal_media').modal({keyboard: false, show: true, backdrop: 'static'});
                    } else {
                    }
                },
                error: function (error) {
                }
            });
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
        },
        setupAddImageButton:function(btnId, wrapperId, namePrefix, classPrefix) {
            let count = $(btnId).find("span").attr("attr-count");
            let index = count;
        
            $(btnId).click(function () {
                const html = `
                <div class="input_${namePrefix}">
                    <div class="form-group">
                        <label class="control-label col-lg-2">Banner</label>
                        <div class="col-lg-3">
                            <input type="file" class="file-styled form-control ${classPrefix}_dm" name="${namePrefix}[${index}]" accept="image/*"/>
                        </div>
                        <label class="control-label col-lg-1">URL</label>
                        <div class="col-lg-3">
                            <input type="text" class="file-styled form-control url_${classPrefix}" name="url_${namePrefix}[${index}]"/>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-alert remove_input_${namePrefix}" style="margin-right: 11px;">x</button>
                        </div>
                    </div>
                </div>`;
                $(wrapperId).append(html);
                index++;
            });
        },
        setupImageLimit:function (buttonSelector, imageClass, maxCount = 100) {
            function checkLimit() {
                var count = $(imageClass).length;
                if (count >= maxCount) {
                    $(buttonSelector).prop("disabled", true);
                }
            }
        
            $(document).ready(function () {
                checkLimit();
                $(buttonSelector).click(function () {
                    checkLimit();
                });
            });
        },
        handleRemoveImage:function (selector, dataAttr, targetContainer, inputName, wrapperClass) {
            $(document).on('click', selector, function(e) {
                e.preventDefault();
                var val = $(this).attr(dataAttr);
                $(targetContainer).append('<input type="hidden" name="'+inputName+'" value="'+val+'">');
                $(this).closest(wrapperClass).remove();
            });
        }
    }
});