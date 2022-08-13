// preview process
document.getElementById('post_image').addEventListener('change', function (e) {
    let file = e.target.files[0];

    if (file.size > 1000000) {
        alert('1MB以下の画像を選択してください');
        this.value = '';
        return false;
    }

    let blobUrl = window.URL.createObjectURL(file);
    let img = document.getElementById('preview_image');
    img.src = blobUrl;
});