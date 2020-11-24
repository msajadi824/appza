jQuery.fn.formCollection = function(addButtonTitle, removeButtonTitle, addButtonClick) {
    var collectionHolder = $(this[0]);
    var index = collectionHolder.children().length;

    var addButton = $('<a class="btn btn-info" href="#">'+ (addButtonTitle || $(collectionHolder).attr('add_title') || 'جدید') +'</a>');
    addButton.on('click', function(e) {
        e.preventDefault();

        var newForm = $(collectionHolder.data('prototype').replace(/label__/g, '').replace(/__name__/g, index  ++));
        deleteButtonInForm(newForm);
        addButtonWrapper.before(newForm);

        if(addButtonClick) addButtonClick(newForm);
    });

    var addButtonWrapper = $('<div><div class="form-group"><div class="col-sm-2"></div><div class="col-sm-10" id="form_collection_add"></div></div><hr/></div>');
    addButtonWrapper.find('#form_collection_add').append(addButton);
    collectionHolder.append(addButtonWrapper);

    var formToDelete;
    var deleteButtonInForm = function(form) {
        var removeButton = $('<a class="btn btn-sm btn-danger" href="#">'+ (removeButtonTitle || $(collectionHolder).attr('remove_title') || 'حذف') +'</a>');
        removeButton.on('click', function(e) {
            e.preventDefault();
            formToDelete = form;
            myModal('حذف', 'آیا این مورد حذف شود؟', 'YesNo', function () { formToDelete.remove(); });
        });

        form.find('legend').first().html(removeButton);
    };

    collectionHolder.children().each(function () {
        deleteButtonInForm($(this));
    });

    return this;
};