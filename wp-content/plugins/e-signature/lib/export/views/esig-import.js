
jQuery(document).ready(function ($) {

    var esigImport = {
        
        _this: null,
        request: null,
        count: 0,
        ajaxResponse : null,
        /**
         * The emails fields that should be validated with BriteVerify
         * @type jQuery
         */
        $submitField: $('#esig-import-submit'),
        /**
         * Initialize
         */
        init: function () {
            _this = this;
            _this.bind();
        },
        bind: function () {

            _this.$submitField.on('click', _this.uploadFile);
        },
        /**
         * Performs validation.
         */
        uploadFile: function (e) {

            e.preventDefault();
            var $this = $(this);
            $("#esig-import-progress-bar").show();
            $(".progress").show();
            var formData = new FormData();
            var fileData = $("#approveme-import-file").prop("files")[0];
            formData.append('action', 'esig_import_handle_upload');
            formData.append('aproveme_import', fileData);
            formData.append('nonce', esigImportData.nonce);
            $.ajax({
                url: esigImportData.url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                type: 'POST',
                beforeSend: _this.uploadBeforeSend,
                success: function (resp) {

                    var file = resp.file;
                    var fileId = resp.id;
                    if (fileId) {
                        _this.processImport(resp);
                    }

                },
                xhr: _this.xhrUploadProgress,
            });
        },
        uploadBeforeSend: function () {
            var overlay = $('<div class="page-loader-overlay"></div>').appendTo('body');
            $(overlay).show();
            $(".progress").before("<div class='load'><p>Uploading file please wait...</p></div>");
        },
        xhrUploadProgress: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var perc = (e.loaded / e.total) * 100;
                        perc = Math.abs(perc.toFixed(2));
                        var countup = perc + '%';
                        $(".progress").css('width', countup);
                        $(".countup").html(countup);
                        $("div.load").html("<p>Uploading file please wait...</p>");
                    }
                }, false);
            }
            return myXhr;
        },
        processImport: function (response) {
            response.startNumber = 0;
            response.finishedImport = 0;
            response.table = 0;
            response.progress = 1;
            _this.processTable(response);
        },
        processTable: function (response) {
            
            var filePath = false;
            var fileId = false;
            var startNumber = 0;
            var importFinished = 0;
            if (!_this.empty(response.file)) {
                filePath = response.file;
            }
            if (!_this.empty(response.id)) {
                fileId = response.id;
            }
            if (!_this.empty(response.startNumber)) {
                startNumber = response.startNumber;
            }
            if (!_this.empty(response.importFinished)) {
                importFinished = response.importFinished;
            }
            if (importFinished) {
                _this.returnFalse;
            }

            var formData = new FormData();
            formData.append('action', 'esig_run_import');
            formData.append('filePath', filePath);
            formData.append('fileId', fileId);
            formData.append('startNumber', startNumber);
            formData.append('importFinished', importFinished);
            formData.append('importTable', response.table);
            formData.append('progress', response.progress);
            formData.append('nonce', esigImportData.nonce);
            $.ajax({
                url: esigImportData.url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                type: 'POST',
                beforeSend: _this.processBeforeSend(response),
                success: function (data) {
                        
                    if (_this.empty(data.id)) {
                        _this.error(data);
                        return false;
                    }
                    // console.log(data);
                    if (data.importFinished) {
                        _this.afterFinished(data);

                    }
                    else {
                         _this.ajaxResponse = data;
                        _this.processAfterSend(data);
                        if (data.progress == 100) {
                            data.progress = 1;
                        }
                        //console.log(data);
                        _this.processTable(data);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    //alert(XMLHttpRequest.responseText);
                    if (_this.empty(XMLHttpRequest.responseText)) {
                        _this.processTable(_this.ajaxResponse);
                    }
                    else {
                        alert(XMLHttpRequest.responseText);
                    }
                    // console.log(errorThrown);
                }

            });
        },
        error: function (response) {
            console.log(response);    
            $("div.load").html("<p>There is a error to import database</p>");
            var countup = response.progress + '%';
            $(".progress").css('width', countup);
            $(".countup").html(countup);
            //window.location.reload();
        },
        afterFinished: function (response) {

            $("div.load").html("<p>Database import successfully completed. Redirecting please wait...</p>");
            var countup = response.progress + '%';
            $(".progress").css('width', countup);
            $(".countup").html(countup);
            window.location.reload();
        },
        processAfterSend: function (response) {

            //  $("div.load").html("<div class='load'><p>Importing table " + esigImportData.tables[response.table] + "</p></div>");
            var countup = response.progress + '%';
            $(".progress").css('width', countup);
            $(".countup").html(countup);
        },
        processBeforeSend: function (response) {
            $("div.load").html("<p>Importing table " + esigImportData.tables[response.table] + "</p>");
            var countup = response.progress + '%';
            $(".progress").css('width', countup);
            $(".countup").html(countup);
        },
        returnFalse: function () {
            return false;
        },
        debounce: function (func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate)
                        func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow)
                    func.apply(context, args);
            };
        },
        empty: function (v) {

            let type = typeof v;
            if (type === 'undefined') {
                return true;
            }
            if (type === 'boolean') {
                return !v;
            }
            if (v === null) {
                return true;
            }
            if (v === undefined) {
                return true;
            }
            if (v instanceof Array) {
                if (v.length < 1) {
                    return true;
                }
            }
            else if (type === 'string') {
                if (v.length < 1) {
                    return true;
                }
                if (v === '0') {
                    return true;
                }
            }
            else if (type === 'object') {
                if (Object.keys(v).length < 1) {
                    return true;
                }
            }
            else if (type === 'number') {
                if (v === 0) {
                    return true;
                }
            }
            return false;
        }
    };
    esigImport.init();
});
