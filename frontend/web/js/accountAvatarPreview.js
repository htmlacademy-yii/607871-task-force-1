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
})();