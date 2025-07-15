jQuery(document).ready(function($) {
    $(".delete-lead").on("click", function(e) {
        e.preventDefault();
        var leadId = $(this).data("id");
        if (confirm("يسطي انت متاكد انك هتحذف الليد ده ؟")) {
            $.ajax({
                url: ajax_object.ajax_url,
                type: "POST",
                data: {
                    action: "delete_lead",
                    lead_id: leadId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data);
                    }
                }
            });
        }
    });
});


jQuery(document).ready(function($) {
    $('.contacted-checkbox').on('change', function() {
        var checkbox = $(this);
        var contacted = checkbox.is(':checked') ? 1 : 0;
        var id = checkbox.data('id');

        $.ajax({
            url: ajax_object.ajax_url, 
            method: 'POST',
            data: {
                action: 'update_contacted_status',
                id: id,
                contacted: contacted
            },
            success: function(response) {
                if (response.success) {
                    console.log('Status updated successfully.');
                } else {
                    alert('Error updating status.');
                }
            },
            error: function() {
                alert('AJAX request failed.');
            }
        });
    });
});



jQuery(document).ready(function($) {
    const popup = $('#popup');
    const popupContent = $('#popup-content');
    const overlay = $('#popup-overlay');
    const closePopup = $('#close-popup');

    $('.open-popup').on('click', function() {
        const rowData = $(this).data('row');
        const content = `
            <p><strong>الاسم:</strong> ${rowData.name}</p>
            <p><strong>رقم الهاتف:</strong> ${rowData.phone}</p>
            <p><strong>التايم زون:</strong> ${rowData.time_zone}</p>
            <p><strong>عنوان الصفحة:</strong> <pre>${rowData.page_title}</pre></p>
            <p><strong>وقت الارسال:</strong> ${rowData.submission_date}</p>
            <p><strong>تم التواصل:</strong> ${rowData.contacted == 1 ? "نعم" : "لا"}</p>
        `;
        popupContent.html(content);
        popup.show();
        overlay.show();
    });

    closePopup.on('click', function() {
        popup.hide();
        overlay.hide();
    });

    overlay.on('click', function() {
        popup.hide();
        $(this).hide();
    });
});


jQuery(document).ready(function ($) {
    const searchInput = $('#related_project_search');
    const resultsDiv = $('#project_search_results');
    const selectedProjectLabel = $('#selected_project_label');
    const relatedProjectIdInput = $('#related_project_id');

    searchInput.on('input', function () {
        const searchTerm = searchInput.val();

        if (searchTerm.length < 3) {
            resultsDiv.html('');
            return;
        }

        $.ajax({
            url: ajax_object.ajax_url,
            method: 'GET',
            data: {
                action: 'search_projects',
                term: searchTerm
            },
            dataType: 'json',
            success: function (data) {
                resultsDiv.html('');
                data.forEach(function (project) {
                    const div = $('<div></div>')
                        .text(project.title)
                        .css('cursor', 'pointer')
                        .data('id', project.id)
                        .data('name', project.title)
                        .on('click', function () {
                            selectedProjectLabel.text(project.title);
                            relatedProjectIdInput.val(project.id);
                            resultsDiv.html('');
                            searchInput.val('');
                        });
                    resultsDiv.append(div);
                });
            }
        });
    });
});




jQuery(document).ready(function($){
    jQuery(document).on('change', 'label.switch input', function() {
        $value = $(this).is(":checked");
        if($value){
            $(this).parent().find('.hide').attr('value', 1);
        }else{
            $(this).parent().find('.hide').attr('value', 0);
        }
    })
})


jQuery(document).ready(function($) {
    var fieldIndex = 0;


    var savedFields = $('#dynamic_fields_data').val();
    if (savedFields) {
        savedFields = JSON.parse(savedFields);
        savedFields.forEach(function(field, index) {
            addField(field.image, field.text, field.link, index);
        });
    }

    function addField(image = '', text = '', link = '', index = fieldIndex) {
        var newField = `
            <div class="field-wrapper-img" data-index="` + index + `">
                <h4>Group ` + (index + 1) + `</h4>
                <label for="image_` + index + `">Image</label>
                <input type="text" id="image_` + index + `" value="` + image + `" />
                <button type="button" class="upload_image_button button" data-input="image_` + index + `">Upload Image</button>
                <br>
                <label for="text_` + index + `">Text</label>
                <input type="text" id="text_` + index + `" value="` + text + `" placeholder="Enter text" />
                <br>
                <label for="link_` + index + `">Link</label>
                <input type="text" id="link_` + index + `" value="` + link + `" placeholder="Enter link" />
                <button type="button" class="remove-field-img button">Remove</button>
                <br><br>
            </div>
        `;
        $('#dynamic-fields-container-img').append(newField);
        fieldIndex++;
        updateHiddenInput();
    }

    function updateHiddenInput() {
        var data = [];
        $('.field-wrapper-img').each(function() {
            var index = $(this).data('index');
            var image = $('#image_' + index).val();
            var text = $('#text_' + index).val();
            var link = $('#link_' + index).val();
            data.push({
                image: image,
                text: text,
                link: link
            });
        });
        $('#dynamic_fields_data').val(JSON.stringify(data));
    }

    $(document).on('click', '.upload_image_button', function(e) {
        e.preventDefault();
        var button = $(this);
        var inputId = button.data('input');
        var custom_uploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false 
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#' + inputId).val(attachment.url); 
            updateHiddenInput(); 
        }).open();
    });

    $('#add-new-field-img').click(function() {
        addField();
    });

    $(document).on('click', '.remove-field-img', function() {
        $(this).closest('.field-wrapper-img').remove();
        updateHiddenInput();
    });

    $(document).on('input', '.field-wrapper-img input', function() {
        updateHiddenInput();
    });
});



jQuery(document).ready(function($) {
    var fieldIndex = 0;

    var savedFields = $('#custom_meta_data').val();
    if (savedFields) {
        savedFields = JSON.parse(savedFields);
        savedFields.forEach(function(field_set, index) {
            addField(field_set.downPayment, field_set.installment, index);
        });
    }

    function addField(downPayment = '', installment = '', index = fieldIndex) {
        var newField = `
            <div class="field-wrapper" data-index="` + index + `">
                <label>النص الاول</label>
                <input type="text" name="custom_meta_fields[` + index + `][downPayment]" value="` + downPayment + `" placeholder="النص الاول" />
                <label>النص التاني</label>
                <input type="text"  name="custom_meta_fields[` + index + `][installment]" value="` + installment + `" placeholder="النص التاني" /> 
                <button type="button" class="button remove-fields-button">حذف الحقول</button>
            </div><br>
        `;
        $('#dynamic-fields-container').append(newField);
        fieldIndex++;
        updateHiddenInput();
    }

    function updateHiddenInput() {
        var data = [];
        $('.field-wrapper').each(function() {
            var index = $(this).data('index');
            var downPayment = $(this).find('input[name^="custom_meta_fields[' + index + '][downPayment]"]').val();
            var installment = $(this).find('input[name^="custom_meta_fields[' + index + '][installment]"]').val();
            data.push({
                downPayment: downPayment,
                installment: installment
            });
        });
        $('#custom_meta_data').val(JSON.stringify(data));
    }

    $('#add-fields-button').click(function() {
        addField();
    });

    $(document).on('click', '.remove-fields-button', function() {
        $(this).closest('.field-wrapper').remove();
        updateHiddenInput();
    });

    $(document).on('input', '.field-wrapper input', function() {
        updateHiddenInput();
    });
});





jQuery(document).ready(function($) {
    var titleFieldIndex = 0;

    var savedTitleFields = $('#custom_meta_data_titles').val();
    if (savedTitleFields) {
        savedTitleFields = JSON.parse(savedTitleFields);
        savedTitleFields.forEach(function(field_set, index) {
            addTitleField(field_set.title, field_set.value, index);
        });
    }

    function addTitleField(title = '', value = '', index = titleFieldIndex) {
        var newField = `
            <div class="title-field-wrapper" data-index="` + index + `">
                <label>عنوان</label>
                <input type="text" name="custom_title_fields[` + index + `][title]" value="` + title + `" placeholder="عنوان" />
                <label>القيمة</label>
                <input type="text" name="custom_title_fields[` + index + `][value]" value="` + value + `" placeholder="القيمة" />
                <button type="button" class="button remove-title-fields-button">حذف الحقل</button>
            </div><br>
        `;
        $('#dynamic-titles-container').append(newField);
        titleFieldIndex++;
        updateHiddenTitleInput();
    }

    function updateHiddenTitleInput() {
        var data = [];
        $('.title-field-wrapper').each(function() {
            var index = $(this).data('index');
            var title = $(this).find('input[name^="custom_title_fields[' + index + '][title]"]').val();
            var value = $(this).find('input[name^="custom_title_fields[' + index + '][value]"]').val();
            data.push({
                title: title,
                value: value
            });
        });
        $('#custom_meta_data_titles').val(JSON.stringify(data));
    }

    $('#add-titles-button').click(function() {
        addTitleField();
    });

    $(document).on('click', '.remove-title-fields-button', function() {
        $(this).closest('.title-field-wrapper').remove();
        updateHiddenTitleInput();
    });

    $(document).on('input', '.title-field-wrapper input', function() {
        updateHiddenTitleInput();
    });
});






jQuery(document).ready(function($) {
    var savedImages = $('#image_gallery_data').val();
    if (savedImages) {
        savedImages = JSON.parse(savedImages);
        savedImages.forEach(function(imageUrl) {
            displayImage(imageUrl);
        });
    }

    $('#upload_gallery_images_button').click(function(e) {
        e.preventDefault();

        var custom_uploader = wp.media({
            title: 'Select Images',
            button: {
                text: 'Use these images'
            },
            multiple: true 
        }).on('select', function() {
            var attachments = custom_uploader.state().get('selection').toJSON();
            var imageUrls = [];

            attachments.forEach(function(attachment) {
                displayImage(attachment.url);
                imageUrls.push(attachment.url);
            });

            updateHiddenInput(imageUrls);
        }).open();
    });

    function displayImage(imageUrl) {
        var imageHtml = `
            <div class="gallery-image-wrapper">
                <img src="` + imageUrl + `" width="100" height="100" />
                <button type="button" class="button remove-gallery-image">Remove</button>
            </div>
        `;
        $('#gallery-images-wrapper').append(imageHtml);
    }

    function updateHiddenInput(newImages = []) {
        var currentImages = $('#image_gallery_data').val();
        currentImages = currentImages ? JSON.parse(currentImages) : [];

        currentImages = currentImages.concat(newImages);

        $('#image_gallery_data').val(JSON.stringify(currentImages));
    }

    $(document).on('click', '.remove-gallery-image', function() {
        var imageWrapper = $(this).closest('.gallery-image-wrapper');
        var imageUrl = imageWrapper.find('img').attr('src');

        var currentImages = $('#image_gallery_data').val();
        currentImages = currentImages ? JSON.parse(currentImages) : [];
        currentImages = currentImages.filter(function(image) {
            return image !== imageUrl;
        });

        $('#image_gallery_data').val(JSON.stringify(currentImages));

        imageWrapper.remove();
    });
});








jQuery(document).ready(function($) {
    function custom_upload_image_button(event) {
        event.preventDefault();

        var button = $(this);
        var customUploader = wp.media({
            title: 'Choose Image',
            library: {
                type: 'image'
            },
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            button.siblings('input[type="text"]').val(attachment.url);
            button.siblings('.image_prev').css('background-image', 'url(' + attachment.url + ')');
        }).open();
    }

    $(document).on('click', '.custom_upload_image_button', custom_upload_image_button);
});




jQuery(document).ready(function($) {
    if (window.location.pathname.indexOf('term.php') !== -1 || window.location.pathname.indexOf('user-edit.php') !== -1 || window.location.pathname.indexOf('profile.php') !== -1) {
        if ($('#description').length) {

            wp.editor.remove('description');

            if (!tinymce.get('description')) {

                wp.editor.initialize('description', {
                    tinymce: {
                        toolbar1: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | link unlink blockquote',
                        toolbar2: 'formatselect blockquote | undo redo | removeformat',
                        plugins: 'link lists paste',
                        height: 300,
                        wp_autoresize_on: true, 
                        resize: true 
                    },
                    quicktags: true, 
                    mediaButtons: true 
                });
            }
        }
    }
});













jQuery(document).ready(function ($) {
    const container = $("#dynamic-section-container");
    const addTitleButton = $("#add-title-button");

    // مصفوفة الأيقونات
    const iconsArray = [
        { id: 1, icon: '<i class="fa fa-road" aria-hidden="true"></i>', text: '<i class="fa fa-road" aria-hidden="true"></i>' },
        { id: 2, icon: '<i class="fa fa-plane" aria-hidden="true"></i>', text: '<i class="fa fa-plane" aria-hidden="true"></i>' },
        { id: 3, icon: '<i class="fa fa-graduation-cap" aria-hidden="true"></i>', text: '<i class="fa fa-graduation-cap" aria-hidden="true"></i>' },
        { id: 4, icon: '<i class="fa fa-building" aria-hidden="true"></i>', text: '<i class="fa fa-building" aria-hidden="true"></i>' },
        { id: 5, icon: '' , text: 'بلا ايقون'},
    ];

    const existingData = $("#dynamic-data").val();
    if (existingData) {
        const sections = JSON.parse(existingData);

        sections.forEach((sectionData, sectionIndex) => {
            const section = $("<div>").addClass("dynamic-section");

            const controlsContainer = $("<div>").addClass("section-controls");

            const titleInput = $("<input>")
                .attr("type", "text")
                .attr("name", "title[]")
                .attr("placeholder", "Enter Section Title")
                .addClass("section-title")
                .val(sectionData.title);

            const addFieldsButton = $("<button>")
                .attr("type", "button")
                .text("Add Sub Fields")
                .addClass("button add-fields");

            const deleteSectionButton = $("<button>")
                .attr("type", "button")
                .text("Delete Section")
                .addClass("button delete-section");

            deleteSectionButton.on("click", function () {
                section.remove();
            });

            controlsContainer.append(titleInput, addFieldsButton, deleteSectionButton);
            section.append(controlsContainer);

            if (sectionData.subFields) {
                sectionData.subFields.forEach((subField, fieldIndex) => {
                    const subFieldsContainer = $("<div>").addClass("sub-fields");

                    iconsArray.forEach((iconItem, iconIndex) => {
                        const uniqueId = `icon-${sectionIndex}-${fieldIndex}-${iconIndex}`;

                        const iconWrapper = $("<div>").addClass("icon-wrapper");

                        const iconInput = $("<input>")
                            .attr("type", "radio")
                            .attr("name", `icon-${sectionIndex}-${fieldIndex}`) 
                            .attr("id", uniqueId)
                            .val(iconItem.icon)
                            .prop("checked", subField.icon === iconItem.icon);

                        const iconLabel = $("<label>")
                            .attr("for", uniqueId)
                            .html(iconItem.text);

                        iconWrapper.append(iconInput, iconLabel); 
                        subFieldsContainer.append(iconWrapper); 
                    });

                    const textInput1 = $("<input>")
                        .attr("type", "text")
                        .attr("name", "text1[]")
                        .attr("placeholder", "Enter First Text")
                        .val(subField.text1);

                    const textInput2 = $("<input>")
                        .attr("type", "text")
                        .attr("name", "text2[]")
                        .attr("placeholder", "Enter Second Text")
                        .val(subField.text2);

                    const deleteFieldsButton = $("<button>")
                        .attr("type", "button")
                        .text("Delete Fields")
                        .addClass("button delete-fields");

                    deleteFieldsButton.on("click", function () {
                        subFieldsContainer.remove();
                    });

                    subFieldsContainer.append(textInput1, textInput2, deleteFieldsButton);

                    section.append(subFieldsContainer);
                });
            }

            container.append(section);
        });
    }

    addTitleButton.on("click", function () {
        const sectionIndex = container.children(".dynamic-section").length; 
        const section = $("<div>").addClass("dynamic-section");

        const controlsContainer = $("<div>").addClass("section-controls");

        const titleInput = $("<input>")
            .attr("type", "text")
            .attr("name", "title[]")
            .attr("placeholder", "Enter Section Title")
            .addClass("section-title");

        const addFieldsButton = $("<button>")
            .attr("type", "button")
            .text("Add Sub Fields")
            .addClass("button add-fields");

        const deleteSectionButton = $("<button>")
            .attr("type", "button")
            .text("Delete Section")
            .addClass("button delete-section");

        deleteSectionButton.on("click", function () {
            section.remove();
        });

        controlsContainer.append(titleInput, addFieldsButton, deleteSectionButton);
        section.append(controlsContainer);

        container.append(section);
    });

    $(document).on("click", ".add-fields", function () {
        const section = $(this).closest(".dynamic-section");
        const sectionIndex = container.children(".dynamic-section").index(section);
        const fieldIndex = section.children(".sub-fields").length;

        const subFieldsContainer = $("<div>").addClass("sub-fields");

        iconsArray.forEach((iconItem, iconIndex) => {
            const uniqueId = `icon-${sectionIndex}-${fieldIndex}-${iconIndex}`;

            const iconWrapper = $("<div>").addClass("icon-wrapper"); 

            const iconInput = $("<input>")
                .attr("type", "radio")
                .attr("name", `icon-${sectionIndex}-${fieldIndex}`) 
                .attr("id", uniqueId)
                .val(iconItem.icon);

            const iconLabel = $("<label>")
                .attr("for", uniqueId)
                .html(iconItem.text);

            iconWrapper.append(iconInput, iconLabel); 
            subFieldsContainer.append(iconWrapper); 
        });

        const textInput1 = $("<input>")
            .attr("type", "text")
            .attr("name", "text1[]")
            .attr("placeholder", "Enter First Text");

        const textInput2 = $("<input>")
            .attr("type", "text")
            .attr("name", "text2[]")
            .attr("placeholder", "Enter Second Text");

        const deleteFieldsButton = $("<button>")
            .attr("type", "button")
            .text("Delete Fields")
            .addClass("button delete-fields");

        deleteFieldsButton.on("click", function () {
            subFieldsContainer.remove();
        });

        subFieldsContainer.append(textInput1, textInput2, deleteFieldsButton);

        section.append(subFieldsContainer);
    });

    $(document).on("submit", "#post", function () {
        const sections = [];
        $(".dynamic-section").each(function (sectionIndex) {
            const section = {};
            section.title = $(this).find(".section-title").val();
            section.subFields = [];

            $(this)
                .find(".sub-fields")
                .each(function (fieldIndex) {
                    const selectedIcon = $(this).find('input[type="radio"]:checked').val() || "";

                    const subField = {
                        icon: selectedIcon,
                        text1: $(this).find('input[name="text1[]"]').val(),
                        text2: $(this).find('input[name="text2[]"]').val(),
                    };
                    section.subFields.push(subField);
                });

            sections.push(section);
        });

        $("#dynamic-data").val(JSON.stringify(sections));
    });
});
