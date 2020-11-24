function collectionInit($selector, $add_link_text, $input_id, $count, $dataName, $url, $alert) {
    var $collectionHolder = $($selector);
    var $addTagLink = $('<a class="btn btn-info" href="#" class="add_tag_link">'+$add_link_text+'</a>');
    var $newLinkLi = $('<div><div class="form-group"><div class="col-sm-2"></div>' +
        '<div class="col-sm-10" id="addTagLink_button">' +
        '</div></div><hr/></div>');
    $newLinkLi.find('#addTagLink_button').append($addTagLink);

    $collectionHolder.children('div').each(function() {
        CollectionAddTagFormDeleteLink($(this), $dataName, $(this).find('input[type="hidden"].'+$input_id).first().val(), $url, $alert);
    });
    $collectionHolder.append($newLinkLi);
    $collectionHolder.data('index', $count);
    $addTagLink.on('click', function(e) {
        e.preventDefault();
        collectionAddTagForm($collectionHolder, $newLinkLi, $dataName, $url, $alert);
    });
}

function collectionAddTagForm($collectionHolder, $newLinkLi, $dataName, $url, $alert) {
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype.replace(/label__/g, '').replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    var $newFormLi = $('<div></div>').append(newForm);
    $newLinkLi.before($newFormLi);
    CollectionAddTagFormDeleteLink($newFormLi, $dataName, 0, $url, $alert);
}

var delete_data = null;

function CollectionAddTagFormDeleteLink($tagFormLi, $dataName, $itemId, $url, $alert) {
    var $removeFormA = $('<a class="btn btn-sm btn-danger" href="#">حذف</a>');
    $tagFormLi.find('legend').first().html($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();

        delete_data = {
            'tagFormLi': $tagFormLi,
            'dataName':  $dataName,
            'itemId':    $itemId,
            'url':       $url,
            'alert':     $alert
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
    if(delete_data == null) return;

    var $data = {};
    $data[delete_data['dataName']] = delete_data['itemId'];

    if($data[delete_data['dataName']] == 0)
    {
        delete_data['tagFormLi'].remove();
        deleteModalNo();
    }
    else
    {
        waitingDialog.show("لطفا منتظر بمانید...", {dialogSize: 'sm'});
        $.ajax({
            type: "get",
            url: delete_data['url'],
            data: $data,
            cache: false,
            success: function(result) {
                waitingDialog.hide();
                if (result <= 0)
                    delete_data['tagFormLi'].remove();
                else {
                    myModal(
                        'پیام',
                        delete_data['alert'],
                        'OK'
                    );
                }
                deleteModalNo();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                waitingDialog.hide();
                alert('خطا در اتصال به سرور');
                deleteModalNo();
            }
        });
    }
}

function deleteModalNo()
{
    delete_data = null;
}