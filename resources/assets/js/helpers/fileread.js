function getImportFields(checkboxId, fileInputId, model, target) {
    let checkbox = document.getElementById(checkboxId);
    let fieldsBlock = document.getElementById(target);

    if (checkbox.checked == true){
        fieldsBlock.style.display = 'block';
    } else {
        fieldsBlock.style.display = 'none';
        return;
    }

    let fileInput = document.getElementById(fileInputId);

    if (!fileInput.files || !fileInput.files[0]) {
        return;
    }

    let fd = new FormData();
    fd.append('file', fileInput.files[0]);
    fd.append('model', model);

    $.ajax({
        url: app.urls.importGetFields,
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        beforeSend: function() {
            loader.add($('#' + target));
        },
        success: function(html){
            $('#' + target).html(html);
            initComponents('#' + target);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            handlerFailTarget(jqXHR, textStatus, errorThrown, $('#' + target));
        },
    });
}