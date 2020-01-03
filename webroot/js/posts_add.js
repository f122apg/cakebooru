$(function(){
    //image preview
    $('#uploadFile').on('change', function(e) {
        //画像が設定されていれば、revoke
        objectRevoke();
        var url = e.target.files[0];
        $('#imagePreviewer').attr('src', URL.createObjectURL(url));
        $('#imagePreviewer').parent().removeClass('d-none');
    });

    objectRevoke = () => {
        if ($('#imagePreviewer').attr('src')) {
            URL.revokeObjectURL($('#imagePreviewer').attr('src'));
            $('#imagePreviewer').attr('src', '');
            console.dir('revoked');
        }
    };
});