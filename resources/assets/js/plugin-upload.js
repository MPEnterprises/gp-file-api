;(function ( $, window, document, undefined ) {

    "use strict";

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
            url: '',
        };

    function Plugin ( element, options ) {
        this.element = element;
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        init: function () {
            Dropzone.autoDiscover = false;

            this.readSettingsFromElement();

            var self = this;
            //this.wrapperElement = this.element)[0];
            // Place initialization logic here
            // You already have access to the DOM element and
            // the options via the instance, e.g. this.element
            // and this.settings
            // you can add more functions like the one below and
            // call them like so: this.yourOtherFunction(this.element, this.settings).
            console.log(this.getDropzoneOptions());
            $(this.element).addClass('dropzone');
            this.dropzone = new Dropzone(this.element, this.getDropzoneOptions());

            this.loadPrefillFiles();

            this.dropzone.on('sending', function(file, xhr, formData) {
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            });

            this.dropzone.on('removedfile', function (file) {
                $.post(this.options.url + '/' + file.hash, {
                    '_method': 'DELETE',
                });
            });

            this.dropzone.on('success', function (file, response) {
                file.hash = response.hash;

                var hiddenInput = $('<input type="hidden" name="' + self.settings.outputName + '" value="' + file.hash + '">');

                hiddenInput.appendTo($(file.previewElement));
            });
        },
        loadPrefillFiles: function () {
            var prefill = $(this.element).find('.prefill-files').first().children();
            if(prefill.length)
            {
                var dz = this.dropzone;
                prefill.each(function () {
                    var mockFile = {};

                    $(this.attributes).each(function () {
                        mockFile[this.name.replace(/^data-/, '')] = this.value;
                    });

                    dz.options.addedfile.call(dz, mockFile);
                    dz.options.thumbnail.call(dz, mockFile, mockFile.thumb);
                    dz.files.push(mockFile);

                    var addedFile = dz.files[dz.files.length - 1];

                    $(addedFile.previewElement).addClass('dz-success dz-complete');
                });
            }
        },
        getDropzoneOptions: function () {
            return {
                'url': this.settings.url,
                'paramName': this.settings.inputName,
                'addRemoveLinks': true,
                'maxFiles': this.settings.maxFiles,
                //'clickable': this.wrapperElement,
            };
        },
        readSettingsFromElement: function () {
            // Grab each setting from the input itself
            for(var key in this.settings)
            {
                if(this.element.getAttribute(this.toDash(key)) != null)
                {
                    var dashedKey = this.toDash(key);
                    this.settings[key] = this.element.getAttribute(dashedKey);
                    if(!isNaN(this.settings[key]))
                    {
                        this.settings[key] = this.settings[key] * 1;
                    }
                    // Remove the attribute from the element
                    this.element.removeAttribute(dashedKey);
                }
            }

            if(!this.settings.url && typeof URL == 'object')
            {
                // Set the URL as the current page, if not already provided
                this.settings.url = URL.current;
            }
        },
        toDash: function (str) {
            return str.replace(/([A-Z])/g, function($1){return "-"+$1.toLowerCase();});
        },
        toCamel: function (str) {
            return str.replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');});
        }
    });

    $.fn[ pluginName ] = function ( options ) {
        return this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }
        });
    };

})( jQuery, window, document );

$(function () {
    // Initialize any uploaders on the page
    $('.uploader').uploader();
});
