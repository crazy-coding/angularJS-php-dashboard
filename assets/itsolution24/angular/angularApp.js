var angularApp = window.angular.module("angularApp", ["ui.bootstrap", "ngSanitize", "angular-bind-html-compile", "pascalprecht.translate", "ngFileUpload"], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("{{");
    $interpolateProvider.endSymbol("}}");
});

angularApp.constant("API_URL", window.baseUrl);
angularApp.constant("window", window);
angularApp.constant("jQuery", window.jQuery);

angularApp.config(["$httpProvider", function($httpProvider) {
    $httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded; charset=UTF-8";
}]);

// ====================
// start text cutting
// ====================
// usage
// {{some_text | cut:true:100:" ..."}}
angularApp.filter("cut", function () {
    return function (value, wordwise, max, tail) {
        if (!value) return "";
        max = parseInt(max, 10);
        if (!max) return value;
        if (value.length <= max) return value;
        value = value.substr(0, max);
        if (wordwise) {
            var lastspace = value.lastIndexOf(" ");
            if (lastspace !== -1) {

              //Also remove . and , so its gives a cleaner result.
              if (value.charAt(lastspace-1) === "." || value.charAt(lastspace-1) === ",") {
                lastspace = lastspace - 1;
              }
              value = value.substr(0, lastspace);
            }
        }
        return value + (tail || " …");
    };
});

// ================
// end text cutting
// ================

// ====================
// start text replacing
// ====================

angularApp.filter('strReplace', function () {
  return function (input, from, to) {
    input = input || '';
    from = from || '';
    to = to || '';
    return input.replace(new RegExp(from, 'g'), to);
  };
});

// usages
// {{ addText | strReplace:'_':' ' }}


// ====================
// start format decimal
// ====================
// usage
// {{some_text | formatDecimal:2}}
angularApp.filter("formatDecimal", function () {
    return function (value, limit) {
        if (!value) return "0.00";
        return window.formatDecimal(value, limit);
    };
});

// ====================
// end format decimal
// ====================


// ============================
// start html ul list filtering
// ============================

angularApp.directive("filterList", function($timeout) {
    return {
        link: function(scope, element, attrs) {
            var li = Array.prototype.slice.call(element[0].children);
            function filterBy(value) {
                li.forEach(function(el) {
                    el.className = el.textContent.toLowerCase().indexOf(value.toLowerCase()) !== -1 ? "" : "ng-hide";
                });
            }
            scope.$watch(attrs.filterList, function(newVal, oldVal) {
                if (newVal !== oldVal) {
                    filterBy(newVal);
                }
            });
        }
    };
});

// ==========================
// end html ul list filtering
// ==========================


// ================
// start filemanger
// ================

if (window.filemanager == 'ftp') {
    window.angular.module("angularApp").config(["fileManagerConfigProvider", function (config) {
        var defaults = config.$get();
        config.set({
            appName: "angular-filemanager",
            tplPath: "../assets/itsolution24/angular/filemanager/templates",
            listUrl: "../_inc/bridges/php/handler.php",
            uploadUrl: "../_inc/bridges/php/handler.php",
            renameUrl: "../_inc/bridges/php/handler.php",
            copyUrl: "../_inc/bridges/php/handler.php",
            moveUrl: "../_inc/bridges/php/handler.php",
            removeUrl: "../_inc/bridges/php/handler.php",
            editUrl: "../_inc/bridges/php/handler.php",
            getContentUrl: "../_inc/bridges/php/handler.php",
            createFolderUrl: "../_inc/bridges/php/handler.php",
            downloadFileUrl: "../_inc/bridges/php/handler.php",
            downloadMultipleUrl: "../_inc/bridges/php/handler.php",
            compressUrl: "../_inc/bridges/php/handler.php",
            extractUrl: "../_inc/bridges/php/handler.php",
            permissionsUrl: "../_inc/bridges/php/handler.php",
            basePath: "/",
            allowedActions: window.angular.extend(defaults.allowedActions, {
              pickFiles: true,
              pickFolders: false,
            }),
            pickCallback: function(item) {
                window.pickFileCallback(item);
            },
        });
    }]);

 } else {

    window.angular.module("angularApp").config(["fileManagerConfigProvider", function (config) {
        var defaults = config.$get();
        config.set({
            appName: "angular-filemanager",
            tplPath: "../assets/itsolution24/angular/filemanager/templates",
            listUrl: "../_inc/bridges/php-local/index.php",
            uploadUrl: "../_inc/bridges/php-local/index.php",
            renameUrl: "../_inc/bridges/php-local/index.php",
            copyUrl: "../_inc/bridges/php-local/index.php",
            moveUrl: "../_inc/bridges/php-local/index.php",
            removeUrl: "../_inc/bridges/php-local/index.php",
            editUrl: "../_inc/bridges/php-local/index.php",
            getContentUrl: "../_inc/bridges/php-local/index.php",
            createFolderUrl: "../_inc/bridges/php-local/index.php",
            downloadFileUrl: "../_inc/bridges/php-local/index.php",
            downloadMultipleUrl: "../_inc/bridges/php-local/index.php",
            compressUrl: "../_inc/bridges/php-local/index.php",
            extractUrl: "../_inc/bridges/php-local/index.php",
            permissionsUrl: "../_inc/bridges/php-local/index.php",
            basePath: "/",
            allowedActions: window.angular.extend(defaults.allowedActions, {
              pickFiles: true,
              pickFolders: false,
            }),
            pickCallback: function(item) {
                window.pickFileCallback(item);
            },
        });
    }]);
}

angularApp.run(["$rootScope", "BuyingProductModal", "POSFilemanagerModal", "BankingDepositModal", "BankingWithdrawModal", "BankTransferModal", "ExpenseSummaryModal", "SummaryReportModal", "keyboardShortcutModal", "EmailModal", "SupportDeskModal", function($rootScope, BuyingProductModal, POSFilemanagerModal, BankingDepositModal, BankingWithdrawModal, BankTransferModal, ExpenseSummaryModal, SummaryReportModal, keyboardShortcutModal, EmailModal, SupportDeskModal,) {
    $rootScope.BuyingProductModal = BuyingProductModal;
    $rootScope.POSFilemanagerModal = POSFilemanagerModal;
    $rootScope.BankingDepositModal = BankingDepositModal;
    $rootScope.BankingWithdrawModal = BankingWithdrawModal;
    $rootScope.BankTransferModal = BankTransferModal;
    $rootScope.ExpenseSummaryModal = ExpenseSummaryModal;
    $rootScope.SummaryReportModal = SummaryReportModal;
    $rootScope.keyboardShortcutModal = keyboardShortcutModal;
    $rootScope.EmailModal = EmailModal;
    $rootScope.SupportDeskModal = SupportDeskModal;
}]);


// =================================
// start filemanger context menu
// =================================

window.angular.element(window.document).on("click", function() {
    window.angular.element("#context-menu").hide();
});

window.angular.element(window.document).on("contextmenu", ".main-navigation .table-files tr.item-list:has(\"td\"), .item-list", function(e) {
    var menu = window.angular.element("#context-menu");

    if (e.pageX >= window.innerWidth - menu.width()) {
        e.pageX -= menu.width();
    }
    if (e.pageY >= window.innerHeight - menu.height()) {
        e.pageY -= menu.height();
    }

    menu.hide().css({
        left: e.pageX,
        top: e.pageY
    }).appendTo("body").show();
    e.preventDefault();
});

if (! Array.prototype.find) {
    Array.prototype.find = function(predicate) {
        if (this == null) {
            throw new TypeError("Array.prototype.find called on null or undefined");
        }
        if (typeof predicate !== "function") {
            throw new TypeError("predicate must be a function");
        }
        var list = Object(this);
        var length = list.length >>> 0;
        var thisArg = arguments[1];
        var value;

        for (var i = 0; i < length; i++) {
            value = list[i];
            if (predicate.call(thisArg, value, i, list)) {
                return value;
            }
        }
        return undefined;
    };
}

// =================================
// end filemanger context menu
// =================================