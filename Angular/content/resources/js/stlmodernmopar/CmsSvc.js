(function() {
	
	angular.module("CmsApp").factory("CmsSvc", [ "$http", function($http) {
		
		return {
			
			submitAccountCreation: function(formValues, callback) {
				$http({
					method: "POST",
					url: "svc/account/create.php",
					data: formValues
				})
				.success(function(response) {
					callback(response);
				})
				.error(function() {
					callback({ successful: false });
				});
			}
		
		};
		
	}]);
	
})();