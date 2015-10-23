(function() {
	
	var app = angular.module("CmsApp", []);
	
	app.controller("CmsCtrl", [ "$scope", "$location", "CmsSvc", function($scope, $location, CmsSvc) {
		
		var ctrlMap = {
			"index": "Index",
			
			"account": "AccountLanding",
			"create": "Create",
			"login": "Login",
			
			"community": "CommunityLanding",
			
			"support": "SupportLanding",
			"terms": "Terms"
		};
		
		this.websiteTitle = "STL Modern Mopars";
		
		this.loggedIn = false;
		this.busy = false;
		this.page = null;

		var mainPage = "index";
		var openPage = "index";
		var previousPage = "index";
		
		this.init = function() {
			var search = $location.search();
			if(search === undefined || search === null) {
				return;
			}
			
			var startPage = search.page;
			if(startPage === undefined || startPage === null || startPage === "") {
				return;
			}
			
			this.setOpenPage(startPage);
		};
		
		this.getOpenPage = function() {
			return openPage;
		};
		
		this.setOpenPage = function(page) {
			var className = ctrlMap[page];
			if(className === undefined || className === null) {
				return;
			}
			
			previousPage = openPage;
			
			this.busy = true;
			
			var classInst = window[className];
			this.page = new classInst(this, CmsSvc);
			
			mainPage = this.page.mainPage;
			openPage = page;
			
			this.page.open();
			
			$location.search("page", page);
			
			this.busy = false;
		};
		
		this.isOnMainPage = function(checkPage) {
			return mainPage == checkPage;
		};
		
		this.isOnPage = function(checkPage) {
			return openPage == checkPage;
		};
		
		this.hasTitle = function() {
			if(this.page === undefined || this.page === null) {
				return false;
			}
			
			return (this.page.title !== undefined);
		};
		
		this.hasLocation = function() {
			if(this.page === undefined || this.page === null) {
				return false;
			}
			
			return (this.page.location !== undefined);
		};
		
		this.init();
		
	}]);
	
})();