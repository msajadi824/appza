$('body').append(
    '<div id="myModal" class="modal fade">'+
        '<div class="modal-dialog">'+
            '<div class="modal-content">'+
                '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                    '<h4 class="modal-title">پیام</h4>'+
                '</div>'+
                '<div class="modal-body"><p id="modal-message"></p></div>'+
                '<div class="modal-footer"></div>'+
            '</div>'+
        '</div>'+
    '</div>');

$('#myModal').on('shown.bs.modal', function () {
    $('#modal-no').focus();
    $('#modal-ok').focus();
});

/**
 * @param {string} $title
 * @param {string} $body
 * @param {string} $buttonType
 * @param {Object} $buttonYes
 * @param {Object} $buttonNo
 */
function myModal($title, $body, $buttonType, $buttonYes, $buttonNo)
{
    var myModal = $("#myModal");
    myModal.find(".modal-title").html($title);
    myModal.find("#modal-message").html($body);
    switch ($buttonType) {
        case 'YesNo':
        case 'yesno':
        case 'YESNO':
            myModal.find(".modal-footer").html(
                '<button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">خیر</button>' +
                '<button id="modal-yes" type="button" class="btn btn-danger" data-dismiss="modal">بلی</button>'
            );
            if($buttonYes) myModal.find("#modal-yes").on('click', $buttonYes);
            if($buttonNo)  {
                myModal.find("#modal-no").on('click', $buttonNo);
                $('#myModal').on('hidden.bs.modal', $buttonNo);
            }
            break;
        case 'OK':
        case 'ok':
        case 'Ok':
            myModal.find(".modal-footer").html(
                '<button id="modal-ok" type="button" class="btn btn-default" data-dismiss="modal">باشه</button>'
            );
            if($buttonYes) myModal.find("#modal-ok").on('click', $buttonYes);
            break;
    }
    myModal.modal('show');
}

$('a.myModal').click(function (e) {
    e.preventDefault();
    var url = $(this).attr('href');

    myModal(
        $(this).attr('data-modal-title'),
        $(this).attr('data-modal-body'),
        $(this).attr('data-modal-type'),
        function(){window.location.replace(url)}
    );
});

$('a.myModalDelete').click(function (e) {
    e.preventDefault();
    var url = $(this).attr('href');

    myModal(
        'حذف',
        'آیا این مورد حذف شود؟',
        'YesNo',
        function(){window.location.replace(url)}
    );
});

$('a.myModalSure').click(function (e) {
    e.preventDefault();
    var url = $(this).attr('href');

    myModal(
        'هشدار',
        'آیا مطمئنید؟',
        'YesNo',
        function(){window.location.replace(url)}
    );
});

var myModalFormSubmit = null;
$('[type="submit"].myModalSure').click(function (e) {
    if(!myModalFormSubmit) {
        myModalFormSubmit = this;
        e.preventDefault();

        myModal(
            'هشدار',
            'آیا مطمئنید؟',
            'YesNo',
            function(){
                $(myModalFormSubmit).closest('form').submit();
                myModalFormSubmit = null;
            },
            function(){
                myModalFormSubmit = null;
            }
        );
    }
});