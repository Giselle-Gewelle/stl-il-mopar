(function() {
	
	angular.module("CmsApp").factory("CmsSvc", [ "$http", function($http) {
		
		return {
			
			submitAccountCreation: function(formValues, callback) {
				$http({
					method: "POST",
					url: "svc/account_submitCreation.res",
					data: formValues
				})
				.success(function(response) {
					if(response === undefined || response === null || response.returnCode === undefined || response.returnCode === null) {
						callback();
					} else {
						callback(response.returnCode);
					}
				})
				.error(function(response) {
					callback();
				});
			},
			
			checkUsername: function(username, callback) {
				$http({
					method: "GET",
					url: "svc/account_checkUsername.res",
					params: {
						"inputCreateUsername": username
					}
				})
				.success(function(response) {
					if(response === undefined || response === null || response.returnCode === undefined || response.returnCode === null) {
						callback("4");
					} else {
						callback(response.returnCode);
					}
				})
				.error(function(response) {
					callback("4");
				});
			}
		
		};
		
	}]);
	
})();