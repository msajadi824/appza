function collectionInit($selector, $add_link_text, $count, $onNewLinkClick) {
    var $collectionHolder = $($selector);
    var $addTagLink = $('<a class="btn btn-info" href="#" class="add_tag_link">'+$add_link_text+'</a>');
    var $newLinkLi = $('<div><div class="form-group"><div class="col-sm-2"></div>' +
        '<div class="col-sm-10" id="addTagLink_button">' +
        '</div></div><hr/></div>');
    $newLinkLi.find('#addTagLink_button').append($addTagLink);

    $collectionHolder.children('div').each(function() {
        CollectionAddTagFormDeleteLink($(this));
    });
    $collectionHolder.append($newLinkLi);
    $collectionHolder.data('index', $count);
    $addTagLink.on('click', function(e) {
        e.preventDefault();
        collectionAddTagForm($collectionHolder, $newLinkLi, $onNewLinkClick);
    });
}

function collectionAddTagForm($collectionHolder, $newLinkLi, $onNewLinkClick) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype.replace(/label__/g, '').replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    var $newFormLi = $('<div></div>').append(newForm);
    $newLinkLi.before($newFormLi);
    CollectionAddTagFormDeleteLink($newFormLi);
    if($onNewLinkClick) $onNewLinkClick($newFormLi);
}

var delete_data = null;

function CollectionAddTagFormDeleteLink($tagFormLi) {
    var $removeFormA = $('<a class="btn btn-sm btn-danger" href="#">حذف</a>');
    $tagFormLi.find('legend').first().html($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();

        delete_data = {
            'tagFormLi': $tagFormLi
        };

        myModal(
            'حذف',
            'آیا این مورد حذف شود؟',
            'YesNo',
            deleteModalYes,
            deleteModalNo
        );
    });
}

function deleteModalYes()
{
    delete_data['tagFormLi'].remove();
    deleteModalNo();
}

function deleteModalNo()
{
    delete_data = null;
}