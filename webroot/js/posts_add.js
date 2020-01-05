$(function(){
    let vm = new Vue({
        el: '#vue',
        data: {
            tag: {
                tags: [],
                addError: false,
                addErrorMsg: ''
            }
        },
        methods: {
            addTagValidation: function(tag) {
                if (tag === '') {
                    this.tag.addError = true;
                    this.tag.addErrorMsg = jsMessage.Error.tagEmpty;
                    return false;
                }

                //重複チェック
                if (this.tag.tags.some(x => x === tag)) {
                    this.tag.addError = true;
                    this.tag.addErrorMsg = jsMessage.Error.tagDuplicate;
                    return false;
                }

                return true;
            },
            addTag: function() {
                let tag = $('#tag').val().trim();

                this.tag.addError = false;

                if (this.addTagValidation(tag)) {
                    this.tag.tags.push(tag);
                }
            },
            removeTag: function(event) {
                let index = $($(event.target).parent()).attr('data-tag-index');
                this.tag.tags.splice(index, 1);
            }
        }
    });
});

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
        }
    };

    $('#postForm').on('submit', () => {
        objectRevoke();
        return true;
    });
});