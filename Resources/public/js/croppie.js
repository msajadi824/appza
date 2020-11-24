var croppie_pic_file_upload;
var croppie_pic_file_hidden;
var croppie_croppie;

$(function () {
    $('body').append('<div class="modal fade" id="modal_croppie" tabindex="-1" role="dialog" style="z-index: 999999;">\n' +
        '        <div class="modal-dialog" role="document" style="width: 830px; max-width: 830px;">\n' +
        '            <div class="modal-content p-0">\n' +
        '                <div class="modal-header flex-row-reverse p-3 mb-2">\n' +
        '                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
        '                    <h4 class="modal-title">برش تصویر</h4>\n' +
        '                </div>\n' +
        '                <div class="modal-body">\n' +
        '                    <div id="modal_croppie_croppie"></div>\n' +
        '                </div>\n' +
        '                <div class="modal-footer">\n' +
        '                    <div class="pull-right ml-auto">\n' +
        '                        <button class="btn btn-secondary modal_croppie_rotate_btn" data-deg="90"><i class="fas fa fa-undo" aria-hidden="true"></i></button>\n' +
        '                        <button class="btn btn-secondary modal_croppie_rotate_btn" data-deg="-90"><i class="fas fa fa-redo fa-repeat" aria-hidden="true"></i></button>\n' +
        '                    </div>\n' +
        '                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>\n' +
        '                    <button type="button" class="btn btn-success" id="modal_croppie_accept_btn">تایید</button>\n' +
        '                </div>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>');

    $('#modal_croppie').on('shown.bs.modal', function () {
        var _this = croppie_pic_file_upload[0];
        if(!_this.files || !_this.files[0]) {
            console.log("Sorry - you're browser doesn't support the FileReader API");
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            croppie_croppie.croppie('bind', {
                url: e.target.result
            }).then(function(){
                console.log('file upload bind complete.');
            });
        };
        reader.readAsDataURL(_this.files[0]);
    });

    $('.modal_croppie_rotate_btn').on('click', function() {
        croppie_croppie.croppie('rotate', parseInt($(this).data('deg')));
    });

    $('#modal_croppie_accept_btn').on('click', function () {
        croppie_croppie.croppie('result', {
            type: 'base64',
            size: 'viewport'
        }).then(function (resp) {
            croppie_pic_file_hidden.val(resp);
            $('#modal_croppie').modal('hide');
        });
    });
});