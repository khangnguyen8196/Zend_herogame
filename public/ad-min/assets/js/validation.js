$(function () {
    pages.validation.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    validation: {
        validator: {},
        init: function () {

        },
        setupValidation: function (selector, vOptions) {
            var options = {
                ignore: 'input[type=hidden], .select2-input', // ignore hidden fields
                errorClass: 'validation-error-label',
                successClass: 'validation-valid-label',
                highlight: function (element, errorClass) {
                    $(element).removeClass(errorClass);
                    $("#"+$(element).attr('id')+'-error').removeClass('hidden');
                },
                unhighlight: function (element, errorClass) {
                    $(element).removeClass(errorClass);
                },
                // Different components require proper error label placement
                errorPlacement: function (error, element) {
                    // Styled checkboxes, radios, bootstrap switch
                    if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container')) {
                        if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                            error.appendTo(element.parent().parent().parent().parent());
                        } else {
                            error.appendTo(element.parent().parent().parent().parent().parent());
                        }
                    }

                    // Unstyled checkboxes, radios
                    else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                        error.appendTo(element.parent().parent().parent());
                    }

                    // Inline checkboxes, radios
                    else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                        error.appendTo(element.parent().parent());
                    }

                    // Input group, styled file input
                    else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                        error.appendTo(element.parent().parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                validClass: "validation-valid-label",
                success: function ( label ) {
                    label.addClass("hidden");
                },
            };
            if (typeof vOptions.rules != "undefined") {
                options.rules = vOptions.rules;
            }

            if (typeof vOptions.messages != "undefined") {
                options.messages = vOptions.messages;
            }
            pages.validation.validator[selector] = $(selector).validate(options);
        }
    }
});