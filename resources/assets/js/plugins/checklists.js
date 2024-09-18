$(document).on('click', '#table_checklist .upload-btn', function() {
  $(this)
      .closest('.checklist-row')
      .find('.checklist-file-upload')
      .trigger('click');
});

$(document).on('change', '#table_checklist input[name^="completed"]', function() {
    var checkbox = $(this);
    var container = checkbox.closest('.checklist-row');
    var completed = checkbox.is(":checked") ? 1 : 0;
    var id = container.data('id');
    var url = app.urls.checklistUpdateRowStatus;

    $.ajax({
        type: "POST",
        url: url+id,
        data: {
            completed: completed
        },
        beforeSend: function() {
            loader.add(container);
            $checklist.removeErrorRow(container);
        },
        success: function (response) {
            if (! response.status) {
                checkbox.prop('checked', false);
                $checklist.addErrorRow(container, response.error);
            }
        },
        error: function (response) {
        },
        complete: function() {
            loader.remove(container);
        }
    });

    $checklist.showSignatureField(container.closest('.group'));
});

$(document).on('change', '#table_checklist input[name^="outcome"]', function() {
    var checkbox = $(this);
    var container = checkbox.closest('.checklist-row');
    var id = container.data('id');
    var url = app.urls.checklistUpdateRowOutcome;

    $.ajax({
        type: "POST",
        url: url+id,
        data: {
            outcome: checkbox.val()
        },
        beforeSend: function() {
            loader.add(container);
            $checklist.removeErrorRow(container);
        },
        success: function (response) {
            if (! response.status) {
                checkbox.prop('checked', false);
                $checklist.addErrorRow(container, response.error);
            }
        },
        error: function (response) {
        },
        complete: function() {
            loader.remove(container);

            $checklist.showSignatureField(container.closest('.group'));
        }
    });
});

$(document).on('change', '#table_checklist .checklist-file-upload', function() {
  var container = $(this).closest('.checklist-row');
  var id = container.data('id');
  var data = new FormData();
  var url = app.urls.checklistUploadFile;
  var file = $(this)[0].files[0];

  $checklist.removeErrorRow(container);

  if (! file) {
      $checklist.addErrorRow(container, "{{ trans('front.please_upload_image') }}");

      return;
  }

  data.append("file", file, file.name);

  $.ajax({
      type: "POST",
      dataType: 'json',
      url: url+container.data('id'),
      data: data,
      beforeSend: function() {
          loader.add(container);
      },
      success: function (response) {
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (typeof jqXHR.responseJSON.errors === 'undefined' || ! Object.keys(jqXHR.responseJSON.errors).length) {
            return;
        }

        $checklist.addErrors(container, jqXHR.responseJSON.errors);
      },
      complete: function() {
          loader.remove(container);
          $checklist.refreshRow(container);
      },
      async: true,
      cache: false,
      contentType: false,
      processData: false
  });

  $checklist.enableFileDelete(container);
});

$(document).on('click', '#table_checklist .signature-wrapper .btn.sign', function() {
  var container = $(this).closest('.group');
  var checklistId = container.data('id');
  var url = app.urls.checklistSign + checklistId;
  var signature = container.find('.signature').val();
  var signatureWrapper = container.find('.signature-wrapper');

  if (container.find('input:checkbox:not(:checked)').length) {
      signatureWrapper.addClass('hidden');

      return ;
  }

  $.ajax({
      type: "POST",
      url: url,
      data: {
          signature: signature,
          notes: container.find('textarea[name^="notes"]').val()
      },
      beforeSend: function() {
          loader.add(container);
          container.find('.signature').removeClass('alert-success alert-danger');
      },
      success: function (response) {
          if (response.status) {
              container.find('.signature').addClass('alert-success');
              container.find('.group-heading .time_completed').text(response.completed_at);
              container.find('.status').removeClass('incomplete').addClass('complete');
          } else {
              container.find('.signature').addClass('alert-danger');
              container.find('.status').removeClass('complete').addClass('incomplete');
          }
      },
      error: function (response) {
      },
      complete: function() {
          loader.remove(container);
      }
  });
});

$(document).on('mouseover', '.thumbnail-preview', function() {
  var container = $(this);

  if (! container.hasClass('empty')) {
      return;
  }

  container.find('.full-preview img').attr('src', container.data('url'));
});

var $checklist = {
  enableFileDelete: function (container) {
    container.find('.icon.delete').removeClass('hidden');
  },

  refreshRow: function (container) {
    var url = app.urls.checklistGetRow + container.data('id');

    $.ajax({
        type: "GET",
        url: url,
        success: function (response) {
            container.html($(response).html());
        }
  });
  },

  addErrorRow: function (container, error) {
    container.after('<tr class="danger text-center err-message"><td colspan="5">'+error+'</td></tr>');
    container.addClass('danger');
  },

  addErrors: function (container, errors) {
    this.addErrorRow(container, Object.values(errors).map(function(err) {
        return err.join('<br>');
    }).join('<br>'));
  },

  removeErrorRow: function (container) {
    var errorContainer = container.next('.err-message');

    if (typeof errorContainer && errorContainer.length) {
        errorContainer.remove();
    }

    container.removeClass('danger');
  },

  showSignatureFields: function () {
    var container = $('#table_checklist');

    container.find('.group-collapse').each(function() {
      $checklist.showSignatureField($(this));
    });
  },

  showSignatureField: function (container) {
    var signatureWrapper = container.find('.signature-wrapper');
    var isCompleted = false;

    if (container.find('input[name^="completed"]:checkbox').length) {
        isCompleted = container.find('input[name^="completed"]:checkbox:not(:checked)').length === 0;
    } else {
        var names = {};
        var count = 0;
        $('input:radio', container).each(function() {
            names[$(this).attr('name')] = true;
        });
        $.each(names, function() {
            count++;
        });
        isCompleted = $('input:radio:checked', container).length === count;
    }

    if (isCompleted) {
        signatureWrapper.removeClass('hidden');
    } else {
        signatureWrapper.addClass('hidden');
        container.find('.status').removeClass('complete').addClass('incomplete');
    }
  }
};