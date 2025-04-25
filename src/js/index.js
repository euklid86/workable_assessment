$(function() {
    $('.js-upload-file-loader').hide();
    $("body").on("click", ".js-upload-file", function(e){
        const fileInput = $('#file')[0];
        const file = fileInput.files[0];
        const responseDiv = $('#response')[0];
        const talentPool = $('#talent_pool').prop('checked');
        
        if (!file) {
            responseDiv.innerHTML = '<div class="alert alert-danger">Please select a file.</div>';
            return;
        }

        $('.js-upload-file-loader').show();
        $('.js-upload-file').hide();

        const formData = new FormData();
        formData.append('file', file);
        formData.append('target', 'workable');
        formData.append('action', 'sync_file');
        formData.append('talentPool', talentPool);

        $.ajax({
            url: 'api/crud.php',
            type: "POST",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json"
        }).done(function (response) {
            $('.js-upload-file-loader').hide();
            $('.js-upload-file').show();
            // console.log(response);
        }).fail();
    });
});