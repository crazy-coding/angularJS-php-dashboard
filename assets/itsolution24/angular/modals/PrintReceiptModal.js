window.angularApp.factory("PrintReceiptModal", ["API_URL", "window", "jQuery", "$http", "$rootScope", function (API_URL, window, $, $http, $scope) {
    return function($scope) {

        var customerContact;
        if ($scope.invoiceInfo.customer_mobile && $scope.invoiceInfo.customer_mobile !== "undefined") {
            customerContact = $scope.invoiceInfo.customer_mobile;
        } else if ($scope.invoiceInfo.mobile_number && $scope.invoiceInfo.mobile_number !== "undefined") {
            customerContact = $scope.invoiceInfo.mobile_number;
        } else {
            customerContact = $scope.invoiceInfo.customer_email;
        }

        var receipt_data = {};
        receipt_data.store_name = window.store.name + "\n";

        receipt_data.header = "";
        // receipt_data.header += window.store.name + "\n";
        receipt_data.header += window.store.address + "\n";
        receipt_data.header += window.store.mobile + "\n";
        receipt_data.info += "\n";
        receipt_data.info += "\n";

        receipt_data.info = "";
        receipt_data.info += "Date:" + $scope.invoiceInfo.created_at + "\n";
        receipt_data.info += "Invoice ID:" + $scope.invoiceInfo.invoice_id + "\n";
        receipt_data.info += "Created By:" + $scope.invoiceInfo.by + "\n";
        receipt_data.info += "\n";
        receipt_data.info += "Customer:" + $scope.invoiceInfo.customer_name + "\n";
        receipt_data.info += "Contact:" + customerContact + "\n";
        receipt_data.info += "\n";

        receipt_data.items = "";
        window.angular.forEach($scope.invoiceItems, function($row, key) {
            receipt_data.items += "#" + key+1 + " " + $row.item_name + "\n";
            receipt_data.items += $row.item_quantity + " x " + parseFloat($row.item_price).toFixed(2) + "  =  " + (parseInt($row.item_quantity) * parseFloat($row.item_price)).toFixed(2) + "\n";
            receipt_data.items += '---------------------';
        });

        receipt_data.totals = "";
        receipt_data.totals += "Subtotal: " + parseFloat($scope.invoiceInfo.payable_amount).toFixed(2) + "\n";
        receipt_data.totals += "Discount:" + parseFloat($scope.invoiceInfo.discount_amount).toFixed(2) + "\n";
        receipt_data.totals += "Tax:" + parseFloat($scope.invoiceInfo.order_tax).toFixed(2) + "\n";
        receipt_data.totals += "Grand Total:" + parseFloat($scope.invoiceInfo.payable_amount).toFixed(2) + "\n";
        receipt_data.totals += "Paid Amount:" + parseFloat($scope.invoiceInfo.paid_amount).toFixed(2) + "\n";
        // receipt_data.totals += "Due Amount:" + parseFloat($scope.invoiceInfo.todays_due).toFixed(2) + "\n";
        receipt_data.totals += "Due Amount:" + parseFloat($scope.invoiceInfo.due).toFixed(2) + "\n";
        receipt_data.totals += "balance:" + parseFloat($scope.invoiceInfo.balance).toFixed(2) + "\n";

        receipt_data.footer = "";
        if ($scope.invoiceInfo.invoice_note) {
            receipt_data.footer += $scope.invoiceInfo.invoice_note + "\n\n";
        }  else {
            receipt_data.footer += "Thank you for choosing us.";
        }

        var socket_data = {
            'printer': window.printer,
            'logo': '',
            'text': receipt_data,
            'cash_drawer': '',
        };
        $.get(window.baseUrl + '/_inc/print.php', {data: JSON.stringify(socket_data)});    
    };
}]);