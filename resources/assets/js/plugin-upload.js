/*
 *
 */
;
(function ($, window, document, undefined) {

    "use strict";

    // Sets up all Ajax requests with user's CSRF token.
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var pluginName = "uploader",
        defaults = {
            dropzone: null,
            maxFiles: 1,
            maxWidth: false,
            maxHeight: false,
            minWidth: false,
            minHeight: false,
            maxSize: false,
            inputName: false,
            outputName: false,
            accept: false,
            sortable: true,
            url: '',
        };

    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        init: function () {
            var ele = $(this.element);

            this.setupDom();

            // Turn off dropzone.js auto-initialization
            Dropzone.autoDiscover = false;

            // Gather settings based on the DOM parameters
            this.readSettingsFromElement();

            // Give this plugin's element the default classes
            ele.addClass('dropzone');

            // Initialize dropzone.js
            var ops = this.getDropzoneOptions();
            this.dropzone = new Dropzone(this.element, ops);

            // Initialize sortable
            if(this.settings.sortable && !this.isSingleFileMode())
            {
                ele.addClass('dz-sortable');
                ele.sortable({
                    items: ".dz-preview",
                });
            }

            // Load pre-set files into dropzone
            this.loadPrefillFiles();

            // Listen for dropzone events
            this.setupEvents();
        },
        setupDom: function () {
            //var ele = $(this.element);

            //ele.append('<div class="dz-image-button"></div>');
        },
        applyIconToPreview: function (file) {
            var ele = $(file.previewElement);

            if (!this.fileIsImage(file)) {
                ele.find('.dz-image').append('<i class="thumbnail-icon fa fa-file-text-o"></i>');
            }
        },
        applyHiddenInputs: function (file, preview) {
            var hiddenInput = $('<input type="hidden" name="' + this.settings.outputName + '" value="' + file.hash + '">');

            hiddenInput.appendTo(preview);
        },
        setupEvents: function () {
            var self = this;

            this.dropzone.on('sending', function (file, xhr, formData) {
                // Before dropzone POSTs to the server, make sure the CSRF token is included
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            });

            this.dropzone.on('removedfile', function (file) {
                // POST a file deletion request to the server.
                if (typeof file.hash != 'undefined') {
                    $.post(this.options.url + '/' + file.hash, {
                        '_method': 'DELETE',
                    });
                }
            });

            this.dropzone.on("addedfile", function (file) {
                if (self.isSingleFileMode() && this.files[1] != null) {
                    // the input is limited to one file, so remove others before adding this one
                    this.removeFile(this.files[0]);
                }

                self.applyIconToPreview(file);
            });

            this.dropzone.on('success', function (file, response) {
                // Add the hash into the local file object
                file.hash = response.hash;
                var preview = $(file.previewElement);

                preview.find('.dz-progress').text('');

                self.applyHiddenInputs(file, preview);
            });

            this.dropzone.on('error', function (file, response, xhr) {
                var errorMark = $(file.previewElement).find('.dz-error-mark');

                errorMark.tooltip({
                    'title': response
                }).tooltip('show');
            });

            this.dropzone.on("thumbnail", function (file) {
                // Do the dimension checks you want to do
                if (!self.imageResolutionIsValid(file)) {
                    file.rejectDimensions()
                }
                else if (typeof file.acceptDimensions == 'function') {
                    file.acceptDimensions();
                }
            });

            this.dropzone.on("uploadprogress", function (file, progress) {
                var preview = $(file.previewElement);

                preview.find('.dz-progress').text(progress + '%');
            });
        },
        isSingleFileMode: function () {
            return this.settings.maxFiles == 1;
        },
        imageResolutionIsValid: function (file) {
            var isTooWide = (this.settings.maxWidth && file.width > this.settings.maxWidth);
            var isTooTall = (this.settings.maxHeight && file.height > this.settings.maxHeight);

            var isTooSlim = (this.settings.minWidth && file.width < this.settings.minWidth);
            var isTooShort = (this.settings.minHeight && file.height < this.settings.minHeight);

            if (isTooWide || isTooTall || isTooSlim || isTooShort) {
                // It's outside the specifications.
                return false;
            }

            // It meets specifications.
            return true;
        },
        loadPrefillFiles: function () {
            var self = this;
            var prefill = $(this.element).find('.prefill-files').first().children();
            if (prefill.length) {
                var dz = this.dropzone;
                prefill.each(function () {
                    var mockFile = {};

                    $(this.attributes).each(function () {
                        mockFile[this.name.replace(/^data-/, '')] = this.value;
                    });

                    dz.options.addedfile.call(dz, mockFile);
                    if (typeof mockFile.thumb != 'undefined' && mockFile.thumb) {
                        dz.options.thumbnail.call(dz, mockFile, mockFile.thumb);
                    }
                    dz.files.push(mockFile);

                    var addedFile = dz.files[dz.files.length - 1];
                    var preview = $(addedFile.previewElement);

                    preview.addClass('dz-success dz-complete');

                    self.applyIconToPreview(addedFile);
                    self.applyHiddenInputs(addedFile, preview);
                });
            }
        },
        fileIsImage: function (file) {
            return file.type.match(/image.*/);
        },
        getDropzoneOptions: function () {
            var self = this;
            return {
                'url': this.settings.url,
                'paramName': 'file',
                'acceptedFiles': this.settings.accept,
                'addRemoveLinks': true,
                'maxFiles': this.settings.maxFiles,
                'autoProcessQueue': true,
                'thumbnailWidth': 132,
                'thumbnailHeight': 132,
                accept: function (file, done) {
                    if (self.fileIsImage(file)) {
                        file.acceptDimensions = done;
                        file.rejectDimensions = function () {
                            done("Invalid dimension.");
                        };
                    } else {
                        done();
                    }
                },
                'previewTemplate': '<div class="dz-preview dz-file-preview">' +
                '<div class="dz-image"><img data-dz-thumbnail /></div>' +
                '<div class="dz-details">' +
                '<div class="dz-filename"><span data-dz-name></span></div>' +
                '<div class="dz-size" data-dz-size></div>' +
                '</div>' +
                '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>' +
                '<div class="dz-error-mark"><span>&#9888;</span></div>' +
                '</div>',
            };
        },
        readSettingsFromElement: function () {
            // Grab each setting from the input itself
            for (var key in this.settings) {
                if (this.element.getAttribute(this.toDash(key)) != null) {
                    var dashedKey = this.toDash(key);
                    this.settings[key] = this.element.getAttribute(dashedKey);
                    if (!isNaN(this.settings[key])) {
                        this.settings[key] = this.settings[key] * 1;
                    } else if (this.settings[key] === 'false') {
                        this.settings[key] = false;
                    }
                    // Remove the attribute from the element
                    this.element.removeAttribute(dashedKey);
                }
            }

            if (!this.settings.url && typeof URL == 'object') {
                // Set the URL as the current page, if not already provided
                this.settings.url = URL.current;
            }
        },
        toDash: function (str) {
            return str.replace(/([A-Z])/g, function ($1) {
                return "-" + $1.toLowerCase();
            });
        },
        toCamel: function (str) {
            return str.replace(/(\-[a-z])/g, function ($1) {
                return $1.toUpperCase().replace('-', '');
            });
        }
    });

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);

$(function () {
    $('.uploader').uploader();
});
