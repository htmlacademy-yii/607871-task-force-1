'use strict';
(function () {
    let matches = function (str) {
        let FILE_TYPES = ['jpg', 'jpeg', 'png'];
        return FILE_TYPES.some(function (it) {
            return str.endsWith(it);
        });
    };

    let avatarFileChooser = document.getElementById('upload-avatar');
    let avatarPreview = document.querySelector('.account__redaction-avatar img');
    let portfolioPreviewContainer = document.querySelector('.portfolio__preview-container');
    let portfolioFileChooser = document.getElementById('files');
    let portfolioPreview = document.querySelector('.div-line + .portfolio__preview');

    avatarFileChooser.addEventListener('change', function () {
        let file = avatarFileChooser.files[0];
        let fileName = file.name.toLowerCase();

        if (matches(fileName)) {
            let reader = new FileReader();
            reader.addEventListener('load', function () {
                avatarPreview.src = reader.result;
            });
            reader.readAsDataURL(file);
        }
    });


    let photoLoader = function (str) {
        let img = new Image(90, 90);
        str.addEventListener('load', function () {
            img.src = this.result;
        });
        return img;
    };


    portfolioFileChooser.addEventListener('change', function () {
        let fragmentPhotos = document.createDocumentFragment();
        let fileMass = portfolioFileChooser.files;
        let count = 0;

        for (let i = 0; i < fileMass.length; i++) {
            let fileName = fileMass[i].name.toLowerCase();
            if (matches(fileName)) {
                let reader = new FileReader();
                reader.readAsDataURL(fileMass[i]);
                let photoItem = photoLoader(reader);
                photoItem.title = 'Фото № ' + (++count);
                let previewItem = portfolioPreview.cloneNode(true);
                previewItem.appendChild(photoItem);
                fragmentPhotos.appendChild(previewItem);
            }
        }
        while (portfolioPreviewContainer.firstChild)  {
            portfolioPreviewContainer.removeChild(portfolioPreviewContainer.firstChild);
        }
        portfolioPreviewContainer.appendChild(fragmentPhotos);
    });
})()


/*

'use strict';
(function () {

    var avatarFileChooser = document.querySelector('.ad-form__field input[type=file]');
    var avatarPreview = document.querySelector('.ad-form-header__preview img');
    var housePhotoContainer = document.querySelector('.ad-form__photo-container');
    var houseFileChooser = housePhotoContainer.querySelector('.ad-form__upload input[type=file]');
    var housePreview = housePhotoContainer.querySelector('.ad-form__photo');

    var matches = function (str) {
        var FILE_TYPES = ['jpg', 'jpeg', 'png'];
        return FILE_TYPES.some(function (it) {
            return str.endsWith(it);
        });
    };

    var photoLoader = function (str) {
        var img = new Image(70, 70);
        str.addEventListener('load', function () {
            img.src = this.result;
        });
        return img;
    };


    houseFileChooser.addEventListener('change', function () {
        var fragmentPhotos = document.createDocumentFragment();
        var fileMass = houseFileChooser.files;
        var count = 0;

        for (var i = 0; i < fileMass.length; i++) {
            var fileName = fileMass[i].name.toLowerCase();
            if (matches(fileName)) {
                var reader = new FileReader();
                reader.readAsDataURL(fileMass[i]);
                var photoItem = photoLoader(reader);
                photoItem.title = 'Фото № ' + (++count);
                var previewItem = housePreview.cloneNode(true);
                previewItem.appendChild(photoItem);
                fragmentPhotos.appendChild(previewItem);
            }
        }
        housePhotoContainer.removeChild(housePreview);
        housePhotoContainer.appendChild(fragmentPhotos);
    });
})();
*/
;