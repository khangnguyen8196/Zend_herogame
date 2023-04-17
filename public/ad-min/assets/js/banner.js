$(function () {
    pages.banner.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    banner: {
        isUpdate: -1,
        init: function () {
            var me = this;
            me.initDatatable();
            me.initValidation();
            pages.common.setupDatePicker();
            $('.select-search').select2();
            $('[data-popup="lightbox"]').fancybox({});
            $(document).on('click', '.seach-form', {}, function ( ) {
                pages.common.executeSearchForm('searchForm', 'bannerTable');
            });
            $(document).on('change', '#bannerDetailForm #image' , {}, function () {
	    		if( $(this).val().length > 0 ){ // if have value
	    			$('.clear_image').removeClass('hidden');
	    		} else {
	    			$('.clear_image').addClass('hidden');
	    		}
		    });
			// set up event for clear button
			$(document).on('click',"#clearPath", {}, function () {
	    		$("#bannerDetailForm input[name=image]").val('');
	    		$('.clear_image').addClass('hidden');
		    });
			
            $(document).on('click', '.submit-btn', {}, function ( ) {
                if (pages.validation.validator['#bannerDetailForm'].form() == false) {
                    return false;
                }
                $(".submit-btn").submit();
            });
            // select media event
            $(document).on('click', '#select-media', {}, function ( ) {
                pages.banner.showMedia();
            });
            $(document).on('click', '.edit-item', {}, function ( ) {
                pages.product.isUpdate = $(this).attr('data-update');
                pages.product.showMedia();
            });
            $(document).on('click', '.remove-item', {}, function ( ) {
                var removeClass = $(this).attr('data-remove');
                if ($('.' + removeClass).length > 0) {
                    $('.' + removeClass).remove();
                }
            });
            $(document).on('click', '#is_video', {}, function ( ) {
               if( this.checked == true ){
                   $("#video_url").parent().show();
                   $("#video_url_label").show();
                   $("#link_container").hide();
               }else{
                   $("#video_url").parent().hide();
                   $("#video_url_label").hide();
                   $("#link_container").show();
               }
            });
            if (currController == 'banner' && currAction == 'detail') {
                me.initValidation();
            }
            $(document).on('click', '.media-select-image', {}, function ( ) {
                var id = $(this).attr('data_id');

                if (me.isUpdate == -1) {
                    if ($('.img-item-' + id).length > 0) {
                        $('.close-media-dialog').click();
                        bootbox.alert(translate('this-image-is-already-exists'));
                    } else {
                        var tpl = '';
                        tpl += '<div class="col-lg-2 col-sm-4 parent img-item-' + id + '">';
                        tpl += '<div class="thumbnail">';
                        tpl += '<div class="thumb">';
                        tpl += '<img class="img-media img-thumbnail-item" src="' + $(this).attr('data-src-thumb') + '" alt="">';
                        tpl += '<div class="caption-overflow">';
                        tpl += '<span>';
                        tpl += '<a class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5 container-image" href="' + $(this).attr('data-src') + '" data-popup="lightbox">';
                        tpl += '<i class="icon-plus3"></i>';
                        tpl += '</a>';
                        tpl += '<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5">';
                        tpl += '<i class="icon-cross2 remove-item" data-remove="img-item-' + id + '"></i></a>';
                        tpl += '<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5">';
                        tpl += '<i class="icon-pencil7 edit-item" data-update="' + id + '"></i></a>';
                        tpl += '</span>';
                        tpl += '</div>';
                        tpl += '</div>';
                        tpl += '</div>';
                        tpl += '<input type="hidden" class="hiden-item" name="media_id[]" value="' + id + '">';
                        tpl += '</div>';
                        $('.list-image').append(tpl);

                    }
                } else {
                    // update
                    var oldId = me.isUpdate;
                    if ($('.img-item-' + id).length > 0 && id != oldId) {
                        $('.close-media-dialog').click();
                        bootbox.alert(translate('this-image-is-already-exists'));
                    } else {
                        $('.img-item-' + oldId + ' .img-media').attr('src', $(this).attr('data-src-thumb'));
                        $('.img-item-' + oldId + ' .container-image').attr('href', $(this).attr('data-src'));
                        $('.img-item-' + oldId + ' .remove-item').attr('data-remove', "img-item-" + id);
                        $('.img-item-' + oldId + ' .edit-item').attr('data-update', id);
                        $('.img-item-' + oldId + ' .hiden-item').val(id);
                        $('.img-item-' + oldId).addClass("img-item-" + id);
                        $('.img-item-' + oldId).removeClass('.img-item-' + oldId);
                    }
                }
                me.isUpdate = -1;
                $('.close-media-dialog').click();

            });
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
        initValidation: function () {
            var loptions = {
                rules: {
                    title: {
                        required: true
                    }
                },
                messages: {
                    title: {
                        required: $("#title").attr('data-msg')
                    }
                }
            };
            pages.validation.setupValidation("#bannerDetailForm", loptions);

        },
        initDatatable: function () {
            var aoColumns = [
                {"data": "id"},
                {"data": "title"},
                {"data": "type"},
                {"data": "status"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
				{
				    "render": function (data, type, row) {
				        var label = '';
				        if (row["type"] == 1) {
				            label = 'Banner Chính';
				        } else if (row["type"] == 2) {
				            label = 'Banner Con Trái';
				        } else if (row["type"] == 3) {
				            label = 'Banner Con Giữa';
				        } else if (row["type"] == 4) {
				            label = 'Banner Con Phải';
				        } else if (row["type"] == 5) {
				            label = 'Banner Footer Trên';
				        } else if (row["type"] == 6) {
				            label = 'Banner Footer Dưới';
				        } else if (row["type"] == 7) {
				            label = 'Banner Header (1024x200)';
				        } 
				        return label;
				    },
				    "targets": 2,
				    "orderable": true,
				    "data": "type"
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
                    "targets": 3,
                    "orderable": true,
                    "data": "status"
                },
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/banner/detail/id/' + row.id + '" > <i class="icon-pencil3"></i> ' + translate('edit') + '</a > </li>';
                        action += '<li> <a onclick="pages.banner.deleteBanner(' + row.id + ')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';

                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 4,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#bannerTable", "/admin/banner/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
        deleteBanner: function (id) {
            bootbox.confirm(translate('are-you-sure-want-to-delete-this-banner'), function (result) {
                if (result) {
                    $.ajax({
                        'url': '/admin/banner/delete',
                        'type': 'GET',
                        'data': {id: id},
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                if ($("#bannerTable").length > 0) {
                                    bootbox.alert(translate('delete-banner-success'));
                                    var t = $("#bannerTable").DataTable();
                                    t.draw();
                                }
                            } else {
                                bootbox.alert(translate('delete-banner-fail'));
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