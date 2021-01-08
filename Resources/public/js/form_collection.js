jQuery.fn.formCollection = function(addButtonTitle, removeButtonTitle, addButtonClick, removeButtonClick) {
    let collectionHolder = $(this[0]);
    let index = collectionHolder.children().length;

    let addButton = $('<a class="btn btn-info" href="#">'+ (addButtonTitle || $(collectionHolder).attr('add_title') || 'جدید') +'</a>');
    addButton.on('click', function(e) {
        e.preventDefault();

        let form = $(collectionHolder.data('prototype').replace(/label__/g, '').replace(/__name__/g, index));
        deleteButtonInForm(form, removeButtonClick);
        form.attr('data-collection-index', index);

        let callBack = function () {
            addButtonWrapper.before(form);
        }

        if(addButtonClick) addButtonClick(form, callBack);
        else callBack();

        index++
    });

    let addButtonWrapper = $('<div><div class="form-group"><div class="col-sm-2"></div><div class="col-sm-10" id="form_collection_add"></div></div><hr/></div>');
    addButtonWrapper.find('#form_collection_add').append(addButton);
    collectionHolder.append(addButtonWrapper);

    let formToDelete;
    let deleteButtonInForm = function(form) {
        let removeButton = $('<a class="btn btn-sm btn-danger" href="#">'+ (removeButtonTitle || $(collectionHolder).attr('remove_title') || 'حذف') +'</a>');
        removeButton.on('click', function(e) {
            e.preventDefault();
            formToDelete = form;

            let callBack = function () {
                myModal('حذف', 'آیا این مورد حذف شود؟', 'YesNo', function () { formToDelete.remove(); }, null);
            };

            if(removeButtonClick) removeButtonClick(form, callBack);
            else callBack();
        });

        form.find('legend').first().html(removeButton);
    };

    collectionHolder.children().each(function () {
        deleteButtonInForm($(this));
    });

    return this;
};