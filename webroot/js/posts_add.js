$(() => {
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
            getReservedWordStr: function(reservedWords) {
                let str = '';
                let length = reservedWords.length - 1;
                reservedWords.forEach((w, i) => {
                    str += '[' + w + ']';
                    if (length !== i) {
                        str += ', ';
                    }
                });

                return str;
            },
            addTagValidation: function(tag) {
                //空チェック
                if (tag === '') {
                    this.tag.addError = true;
                    this.tag.addErrorMsg = jsMessage.Error.tagEmpty;
                    return false;
                }

                //予約語チェック
                if (jsConsts.reservedWord.some(w => w === tag)) {
                    this.tag.addError = true;
                    let reservedWords = this.getReservedWordStr(jsConsts.reservedWord);
                    this.tag.addErrorMsg = jsMessage.Error.tagReservedWord.replace('%s', reservedWords);
                    return false;
                }

                //デリミタチェック
                if (tag.indexOf(jsConsts.tagDelimiter) !== -1) {
                    this.tag.addError = true;
                    this.tag.addErrorMsg = jsMessage.Error.tagDelimiterFound.replace('%s', jsConsts.tagDelimiter);
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
                    $('#tag').val('');
                }
            },
            removeTag: function(event) {
                let index = $($(event.target).parent()).attr('data-tag-index');
                this.tag.tags.splice(index, 1);
            },
            tagInputName: function (i) {
                return 'tags[' + i + '][tag]';
            }
        },
    });
});

$(() => {
    //image preview
    $('#uploadFile').on('change', (e) => {
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