define(['uiComponent', 'jquery', 'ko'], function (Component, $, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Renga_Koupload/form',
            isDragging: ko.observable(false)
        },

        initialize: function () {
            this._super();
            console.log('KO Upload initialized');
        },

        handleDragOver: function (data, e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('drag over');
            if (!this.isDragging()) {
                this.isDragging(true);
            }
        },

        handleDragLeave: function (data, e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('leave');
            this.isDragging(false);
        },

        handleDrop: function (data, e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('drop');
            this.isDragging(false);

            const files = e.originalEvent.dataTransfer.files;
            this.uploadFiles(files);
        },

        uploadFiles: function (files) {
            console.log('uploading');
            const formData = new FormData();
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });

            $.ajax({
                url: '/koupload/upload/index',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log('Upload success:', response);
                },
                error: function (xhr, status, error) {
                    console.error('Upload error:', error);
                }
            });
        }
    });
});
