const mix = require('laravel-mix');

// // Main CSS
// mix.styles([

//     // Plugins
//     'assets/bootstrap/css/bootstrap.css',
//     'assets/jquery-ui/jquery-ui.min.css',
//     'assets/font-awesome/css/font-awesome.css',
//     'assets/morris/morris.css',
//     'assets/select2/select2.min.css',
//     'assets/datepicker/datepicker3.css',
//     'assets/timepicker/bootstrap-timepicker.css',
//     'assets/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
//     'assets/perfectScroll/css/perfect-scrollbar.css',
//     'assets/toastr/toastr.min.css',

//     // Filemanager
//     'assets/itsolution24/css/filemanager/dialogs.css',
//     'assets/itsolution24/css/filemanager/main.css',

//     // Theme
//     'assets/itsolution24/css/theme.css',
//     'assets/itsolution24/css/skins/skin-black.css',
//     'assets/itsolution24/css/skins/skin-blue.css',
//     'assets/itsolution24/css/skins/skin-green.css',
//     'assets/itsolution24/css/skins/skin-red.css',
//     'assets/itsolution24/css/skins/skin-yellow.css',

//     // DataTable
//     'assets/DataTables/datatables.min.css',

//     // Main CSS
//     'assets/itsolution24/css/main.css',

//     // Responsive CSS
//     'assets/itsolution24/css/responsive.css',

//     // Print CSS
//     'assets/itsolution24/css/print.css',

// ],'assets/itsolution24/cssmin/main.css');



// // POS CSS
// mix.styles([

//     'assets/bootstrap/css/bootstrap.css',
//     'assets/jquery-ui/jquery-ui.min.css',
//     'assets/font-awesome/css/font-awesome.css',
//     'assets/datepicker/datepicker3.css',
//     'assets/timepicker/bootstrap-timepicker.min.css',
//     'assets/perfectScroll/css/perfect-scrollbar.css',
//     'assets/select2/select2.min.css',
//     'assets/toastr/toastr.min.css',
//     'assets/contextMenu/dist/jquery.contextMenu.min.css',

//     // Filemanager
//     'assets/itsolution24/css/filemanager/dialogs.css',
//     'assets/itsolution24/css/filemanager/main.css',

//     // Theme
//     'assets/itsolution24/css/theme.css',
//     'assets/itsolution24/css/skins/skin-black.css',
//     'assets/itsolution24/css/skins/skin-blue.css',
//     'assets/itsolution24/css/skins/skin-green.css',
//     'assets/itsolution24/css/skins/skin-red.css',
//     'assets/itsolution24/css/skins/skin-yellow.css',
//     'assets/itsolution24/css/main.css',

//     // Main
//     'assets/itsolution24/css/pos/skeleton.css',
//     'assets/itsolution24/css/pos/pos.css',
//     'assets/itsolution24/css/pos/responsive.css',

// ],'assets/itsolution24/cssmin/pos.css');



// // LOGIN CSS
// mix.styles([

//     'assets/bootstrap/css/bootstrap.css',
//     'assets/toastr/toastr.min.css',
//     'assets/itsolution24/css/theme.css',
//     'assets/itsolution24/css/login.css',

// ],'assets/itsolution24/cssmin/login.css');



// Angular JS
mix.scripts([

    'assets/itsolution24/angular/lib/angular/angular.min.js',
    'assets/itsolution24/angular/lib/angular/angular-sanitize.js',
    'assets/itsolution24/angular/lib/angular/angular-bind-html-compile.min.js',
    'assets/itsolution24/angular/lib/angular/ui-bootstrap-tpls-2.5.0.min.js',
    'assets/itsolution24/angular/lib/angular/angular-route.min.js',
    'assets/itsolution24/angular/lib/angular-translate/dist/angular-translate.min.js',
    'assets/itsolution24/angular/lib/ng-file-upload/dist/ng-file-upload.min.js',
    'assets/itsolution24/angular/angularApp.js',
    
],'assets/itsolution24/angularmin/angular.js');

// Angular Filemanager JS
mix.scripts([

    'assets/itsolution24/angular/filemanager/js/directives/directives.js',
    'assets/itsolution24/angular/filemanager/js/filters/filters.js',
    'assets/itsolution24/angular/filemanager/js/providers/config.js',
    'assets/itsolution24/angular/filemanager/js/entities/chmod.js',
    'assets/itsolution24/angular/filemanager/js/entities/item.js',
    'assets/itsolution24/angular/filemanager/js/services/apihandler.js',
    'assets/itsolution24/angular/filemanager/js/services/apimiddleware.js',
    'assets/itsolution24/angular/filemanager/js/services/filenavigator.js',
    'assets/itsolution24/angular/filemanager/js/providers/translations.js',
    'assets/itsolution24/angular/filemanager/js/controllers/main.js',
    'assets/itsolution24/angular/filemanager/js/controllers/selector-controller.js',

],'assets/itsolution24/angularmin/filemanager.js');



// Angular Modal JS
mix.scripts([

    'assets/itsolution24/angular/modals/AddInvoiceNoteModal.js',
    'assets/itsolution24/angular/modals/InvoiceInfoEditModal.js',
    'assets/itsolution24/angular/modals/BarcodePrintModal.js',
    'assets/itsolution24/angular/modals/BoxCreateModal.js',
    'assets/itsolution24/angular/modals/BoxDeleteModal.js',
    'assets/itsolution24/angular/modals/BoxEditModal.js',
    'assets/itsolution24/angular/modals/UnitCreateModal.js',
    'assets/itsolution24/angular/modals/UnitDeleteModal.js',
    'assets/itsolution24/angular/modals/UnitEditModal.js',
    'assets/itsolution24/angular/modals/TaxrateCreateModal.js',
    'assets/itsolution24/angular/modals/TaxrateDeleteModal.js',
    'assets/itsolution24/angular/modals/TaxrateEditModal.js',
    'assets/itsolution24/angular/modals/BuyingInvoiceViewModal.js',
    'assets/itsolution24/angular/modals/BuyingProductModal.js',
    'assets/itsolution24/angular/modals/BuyingInvoiceInfoEditModal.js',
    'assets/itsolution24/angular/modals/CategoryCreateModal.js',
    'assets/itsolution24/angular/modals/CategoryDeleteModal.js',
    'assets/itsolution24/angular/modals/CategoryEditModal.js',
    'assets/itsolution24/angular/modals/CurrencyEditModal.js',
    'assets/itsolution24/angular/modals/CustomerCreateModal.js',
    'assets/itsolution24/angular/modals/CustomerDeleteModal.js',
    'assets/itsolution24/angular/modals/CustomerEditModal.js',
    'assets/itsolution24/angular/modals/SupportDeskModal.js',
    'assets/itsolution24/angular/modals/DueCollectionDetailsModal.js',
    'assets/itsolution24/angular/modals/BankingDepositModal.js',
    'assets/itsolution24/angular/modals/BankingRowViewModal.js',
    'assets/itsolution24/angular/modals/BankingWithdrawModal.js',
    'assets/itsolution24/angular/modals/BankAccountCreateModal.js',
    'assets/itsolution24/angular/modals/BankAccountDeleteModal.js',
    'assets/itsolution24/angular/modals/BankAccountEditModal.js',
    'assets/itsolution24/angular/modals/BankTransferModal.js',
    'assets/itsolution24/angular/modals/EmailModal.js',
    'assets/itsolution24/angular/modals/KeyboardShortcutModal.js',
    'assets/itsolution24/angular/modals/PmethodDeleteModal.js',
    'assets/itsolution24/angular/modals/PmethodEditModal.js',
    'assets/itsolution24/angular/modals/PayNowModal.js',
    'assets/itsolution24/angular/modals/POSFilemanagerModal.js',
    'assets/itsolution24/angular/modals/POSReceiptTemplateEditModal.js',
    'assets/itsolution24/angular/modals/PrinterDeleteModal.js',
    'assets/itsolution24/angular/modals/PrinterEditModal.js',
    'assets/itsolution24/angular/modals/PrintReceiptModal.js',
    'assets/itsolution24/angular/modals/ProductCreateModal.js',
    'assets/itsolution24/angular/modals/ProductDeleteModal.js',
    'assets/itsolution24/angular/modals/ProductEditModal.js',
    'assets/itsolution24/angular/modals/ProductReturnModal.js',
    'assets/itsolution24/angular/modals/ProductViewModal.js',
    'assets/itsolution24/angular/modals/StoreDeleteModal.js',
    'assets/itsolution24/angular/modals/SupplierCreateModal.js',
    'assets/itsolution24/angular/modals/SupplierDeleteModal.js',
    'assets/itsolution24/angular/modals/SupplierEditModal.js',
    'assets/itsolution24/angular/modals/UserCreateModal.js',
    'assets/itsolution24/angular/modals/UserDeleteModal.js',
    'assets/itsolution24/angular/modals/UserEditModal.js',
    'assets/itsolution24/angular/modals/UserGroupCreateModal.js',
    'assets/itsolution24/angular/modals/UserGroupDeleteModal.js',
    'assets/itsolution24/angular/modals/UserGroupEditModal.js',
    'assets/itsolution24/angular/modals/UserInvoiceDetailsModal.js',
    'assets/itsolution24/angular/modals/GiftcardCreateModal.js',
    'assets/itsolution24/angular/modals/GiftcardEditModal.js',
    'assets/itsolution24/angular/modals/GiftcardViewModal.js',
    'assets/itsolution24/angular/modals/GiftcardTopupModal.js',
    'assets/itsolution24/angular/modals/InvoiceSMSModal.js',
    'assets/itsolution24/angular/modals/PaymentFormModal.js',
    'assets/itsolution24/angular/modals/PaymentOnlyModal.js',
    'assets/itsolution24/angular/modals/PurchasePaymentModal.js',
    'assets/itsolution24/angular/modals/SellReturnModal.js',
    'assets/itsolution24/angular/modals/PurchaseReturnModal.js',
    'assets/itsolution24/angular/modals/ExpenseSummaryModal.js',
    'assets/itsolution24/angular/modals/SummaryReportModal.js',
    
],'assets/itsolution24/angularmin/modal.js');



// Main JS
mix.scripts([

    'assets/jquery/jquery.min.js',
    'assets/jquery-ui/jquery-ui.min.js',
    'assets/bootstrap/js/bootstrap.min.js',
    'assets/chartjs/Chart.min.js',
    'assets/sparkline/jquery.sparkline.min.js',
    'assets/datepicker/bootstrap-datepicker.js',
    'assets/timepicker/bootstrap-timepicker.min.js',
    'assets/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
    'assets/select2/select2.min.js',
    'assets/perfectScroll/js/perfect-scrollbar.jquery.min.js',
    'assets/sweetalert/sweetalert.min.js',
    'assets/toastr/toastr.min.js',
    'assets/accounting/accounting.min.js',
    'assets/underscore/underscore.min.js',
    'assets/itsolution24/js/ie.js',
    'assets/itsolution24/js/theme.js',
    'assets/itsolution24/js/common.js',
    'assets/itsolution24/js/main.js',
    'assets/DataTables/datatables.min.js',
    'assets/itsolution24/angularmin/angular.js',
    'assets/itsolution24/angularmin/modal.js',
    'assets/itsolution24/angularmin/filemanager.js',

],'assets/itsolution24/jsmin/main.js');



// POS JS
mix.scripts([

    'assets/jquery/jquery.min.js',
    'assets/jquery-ui/jquery-ui.min.js',
    'assets/bootstrap/js/bootstrap.min.js',
    'assets/itsolution24/angularmin/angular.js',
    'assets/itsolution24/angular/angularApp.js',
    'assets/itsolution24/angularmin/filemanager.js',
    'assets/itsolution24/angularmin/modal.js',

    'assets/datepicker/bootstrap-datepicker.js',
    'assets/timepicker/bootstrap-timepicker.min.js',
    'assets/select2/select2.min.js',
    'assets/perfectScroll/js/perfect-scrollbar.jquery.min.js',
    'assets/sweetalert/sweetalert.min.js',
    'assets/toastr/toastr.min.js',
    'assets/accounting/accounting.min.js',
    'assets/underscore/underscore.min.js',
    'assets/contextMenu/dist/jquery.contextMenu.min.js',
    'assets/itsolution24/js/ie.js',

    'assets/itsolution24/js/common.js',
    'assets/itsolution24/js/main.js',
    'assets/itsolution24/js/pos/pos.js',

],'assets/itsolution24/jsmin/pos.js');


// LOGIN JS
mix.scripts([

    'assets/jquery/jquery.min.js',
    'assets/bootstrap/js/bootstrap.min.js',
    'assets/toastr/toastr.min.js',
    'assets/itsolution24/js/forgot-password.js',
    'assets/itsolution24/js/common.js',
    'assets/itsolution24/js/login.js',

],'assets/itsolution24/jsmin/login.js');



// How to build assets
// npm run dev
// npm run production