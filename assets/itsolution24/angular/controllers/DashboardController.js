window.angularApp.controller("DashboardController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "ProductCreateModal",
    "BoxCreateModal",
    "SupplierCreateModal",
    "CustomerCreateModal",
    "UserCreateModal",
    "UserGroupCreateModal",
    "BuyingProductModal",
    "BankingRowViewModal",
    "EmailModal",
function ($scope,
    API_URL,
    window,
    $,
    $http,
    ProductCreateModal,
    BoxCreateModal,
    SupplierCreateModal,
    CustomerCreateModal,
    UserCreateModal,
    UserGroupCreateModal,
    BuyingProductModal,
    BankingRowViewModal,
    EmailModal
) {
    "use strict";

    // create new product
    $scope.createNewProduct = function () {
        $scope.hideCategoryAddBtn = true;
        $scope.hideSupAddBtn = true;
        $scope.hideBoxAddBtn = true;
        $scope.hideUnitAddBtn = true;
        $scope.hideTaxrateAddBtn = true;
        ProductCreateModal($scope);
    };

    // create new box
    $scope.createNewBox = function () {
        BoxCreateModal($scope);
    };

    // create new supplier
    $scope.createNewSupplier = function () {
        SupplierCreateModal($scope);
    };

    // create new customer
    $scope.createNewCustomer = function () {
        CustomerCreateModal($scope);
    };

    // create new user
    $scope.createNewUser = function () {
        $scope.hideGroupAddBtn = true;
        UserCreateModal($scope);
    };

    // create new usergroup
    $scope.createNewUsergroup = function () {
        UserGroupCreateModal($scope);
    };

    $http({
        url: API_URL + "/~sunny/_inc/pos.php?type=STOCKCHECK",
        method: "GET",
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json"
    }).
    then(function(response) {
        if (response.data.error == true) {
            window.location = window.baseUrl+'/maintenance.php';
        }
    });

    // view deposit details
    $(".view-deposit").on("click", function (e) {
        e.preventDefault();
        var refNo = $(this).data("refno");
        BankingRowViewModal({ref_no: refNo}, 'deposit');
    });

    // view withdraw details
    $(".view-withdraw").on("click", function (e) {
        e.preventDefault();
        var refNo = $(this).data("refno");
        BankingRowViewModal({ref_no: refNo}, 'withdraw');
    });

    if (!window.totalProduct && window.user.group_name == 'admin') {
        $scope.BuyingProductModalCallback = function ($scopeData) {
            //...
        };
        $scope.ProductCreateModalCallback = function ($scopeData) {
            $scope.product = $scopeData.product;
            BuyingProductModal($scope);
        };
        $scope.createNewProduct = function () {
            $scope.hideCategoryAddBtn = true;
            $scope.hideSupAddBtn = true;
            $scope.hideBoxAddBtn = true; 
            $scope.hideUnitAddBtn = true;
            $scope.hideTaxrateAddBtn = true;
            ProductCreateModal($scope);
        };
        $scope.createNewProduct();
    }

}]);