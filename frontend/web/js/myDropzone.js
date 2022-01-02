//$(function () {
    let analyzeResponse = function (data) {
        console.log(data);
    };

    Dropzone.autoDiscover = false;
    new Dropzone('div.create__file', {
        url: '/tasks/create-task',
        autoProcessQueue: false,
        uploadMultiple: true,
        paramName: 'files',
        addRemoveLinks: true,
        dictRemoveFile: 'удалить файл',
        acceptedFiles: 'image/*, .pdf, .docx, .doc, .txt, .xls, .csv',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 5,
        maxFiles: 5,
        maxFilesize: 10,
        previewTemplate: '<div><img data-dz-thumbnail alt="Фото работы"></div>',
        dictMaxFilesExceeded: 'Превышено макс. кол-во файлов. Макс. кол-во: {{maxFiles}}шт.',
        dictFileTooBig: 'Файл слишком большой ({{filesize}}MB). Макс. размер: {{maxFilesize}}MB.',
        errormultiple: function (data) {
            analyzeResponse(data);
        },
        init: function() {
            let dzClosure = this;

            document.getElementById('form-submit-button').
            addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (dzClosure.files.length === 0) {
                    let form = $('form#task-form');
                    let data = form.serialize();

                    $.post({
                        url: '/tasks/create-task',
                        data: data,
                        success: function(data) {
                            //analyzeResponse(data);
                        },
                        error: function(data) {
                            //analyzeResponse(data);
                        }
                    });
                    return false;
                }

                dzClosure.processQueue();
            });

            //send all the form data along with the files:
            this.on('sendingmultiple', function(data, xhr, formData) {
                formData.append('Task[title]', $('#task-title').val());
                formData.append('Task[description]', $('#task-description').val());
                formData.append('Task[category_id]', $('#task-category_id').val());
                formData.append('Task[latitude]', $('#task-latitude').val());
                formData.append('Task[longitude]', $('#task-longitude').val());
                formData.append('Task[budget]', $('#task-budget').val());
                formData.append('Task[due_date]', $('#task-due_date').val());
                formData.append('_csrf-frontend', $('[name="_csrf-frontend"]').val());
            })
        }
    });
//});