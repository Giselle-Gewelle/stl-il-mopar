function Index(ctrl, svc) {

	this.mainPage = "index";
	
	this.testValue = "testing";
	this.index = 0;
	
	this.open = function() {
		this.testValue = "test " + this.index++;
	};
	
}