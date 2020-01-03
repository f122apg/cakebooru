$(() => {
    toBlob = (base64, ext) => {
        var bin = atob(base64.replace(/^.*,/, ''));
        var buffer = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) {
            buffer[i] = bin.charCodeAt(i);
        }

        return new Blob([buffer.buffer], {
            type: 'image/' + ext
        });
    };
});