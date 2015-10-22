function Create(ctrl, service) {

	this.mainPage = "account";
	this.title = "Create an Account";
	this.location = [
		[ "index", "Home" ],
		[ "account", "Account" ]
	];
	this.usernameErrors = [
		"Please input a valid username",
		"Usernames must be between 1 and 15 characters in length. Please choose a new username",
		"The chosen username contains an invalid character. Please choose a new username",
		"That username is already registered to another user. Please choose a new username",
		"An unknown error has occurred, please try again or contact Website Support"
	];
	this.passwordErrors = new Array(
		"Please input a valid password",
		"Passwords must be between 5 and 35 characters in length. Please choose a new password",
		"The chosen password contains an invalid character. Please choose a new password",
		"The passwords entered do not match"
	);
	
	this.fields = {};
	this.errors = {};
	this.creationComplete = false;
	
	this.open = function() {
		ctrl.busy = true;
		
		this.fields = {
			city: "",
			state: "",
			username: "",
			password1: "",
			password2: "",
			terms: false
		};
		this.resetErrors();
		
		this.creationResponse = {
			title: "",
			message: "",
			success: false
		};
		
		ctrl.busy = false;
	};
	
	this.submitDisabled = function() {
		if(ctrl.busy) {
			return true;
		}
		
		if(this.fields.city == "" || this.fields.state == "") {
			return true;
		}
		if(this.fields.username == "") {
			return true;
		}
		if(this.fields.password1 == "" || this.fields.password2 == "") {
			return true;
		}
		if(!this.fields.terms) {
			return true;
		}
		
		return false;
	};
	
	this.submit = function() {
		this.resetErrors();
		
		var returnType = true;
		
		if(!this.validateCityState()) {
			returnType = false;
		}
			
		if(!this.validateUsername()) {
			returnType = false;
		}
		
		if(!this.validatePasswords()) {
			returnType = false;
		}
			
		if(!this.validateTerms()) {
			this.errors.terms = "You must agree to both the Terms of Use and Privacy Policy in order to create an account.";
			returnType = false;
		}
		
		if(returnType) {
			ctrl.busy = true;
			
			var thisObj = this;
			service.submitAccountCreation(this.fields, function(response) {
				if(!response.successful) {
					if(response.cityStateError) {
						thisObj.errors.cityState = "Please input a valid city and state";
					}
					if(response.usernameError !== -1) {
						thisObj.errors.username = thisObj.usernameErrors[response.usernameError];
					}
					if(response.passwordError !== -1) {
						thisObj.errors.password = thisObj.passwordErrors[response.passwordError];
					}
					if(response.termsError) {
						thisObj.errors.terms = "You must agree to both the Terms of Use and Privacy Policy in order to create an account.";
					}
				} else {
					thisObj.creationComplete = true;
				}
				ctrl.busy = false;
			});
		}
	};
	
	this.validateTerms = function() {
		if(!this.fields.terms) {
			return false;
		}
		
		return true;
	};
	
	this.passwordChanged = function() {
		this.validatePasswords();
	};
	
	this.validatePasswords = function() {
		this.errors.password = null;
		
		this.fields.password1 = this.fields.password1.trim();
		if(this.fields.password1 === "") {
			this.errors.password = this.passwordErrors[0];
			return false;
		}
		
		if(this.fields.password1.length < 5 || this.fields.password1.length > 35) {
			this.errors.password = this.passwordErrors[1];
			return false;
		}
		
		if(!this.fields.password1.match(/^[A-Za-z0-9]{5,35}$/)) {
			this.errors.password = this.passwordErrors[2];
			return false;
		}
		
		this.fields.password2 = this.fields.password2.trim();
		if(this.fields.password2 === "" || this.fields.password2 !== this.fields.password1) {
			this.errors.password = this.passwordErrors[3];
			return false;
		}
		
		return true;
	};
	
	this.usernameChanged = function() {
		this.validateUsername();
	};
	
	this.validateUsername = function() {
		this.errors.username = null;
		
		this.fields.username = this.fields.username.trim();
		if(this.fields.username === "") {
			this.errors.username = this.usernameErrors[0];
			return false;
		}
		
		if(this.fields.username.length < 1 || this.fields.username.length > 15) {
			this.errors.username = this.usernameErrors[1];
			return false;
		}
		
		if(!this.fields.username.match(/^[A-Za-z0-9 ]{1,15}$/)) {
			this.errors.username = this.usernameErrors[2];
			return false;
		}
		
		while(this.fields.username.indexOf('  ') !== -1) {
			this.fields.username = this.fields.username.replace(/  /g, ' ');
		}
		
		return true;
	};
	
	this.cityStateChanged = function() {
		this.validateCityState();
	};
	
	this.validateCityState = function() {
		this.errors.cityState = null;
		
		if(this.fields.state !== "IL" && this.fields.state !== "MO" && this.fields.state !== "O") {
			this.errors.cityState = "Please input a valid city and state";
			return false;
		}
		
		this.fields.city = this.fields.city.trim();
		if(this.fields.city === "" || !this.fields.city.match(/^[A-Za-z '-]{3,50}$/)) {
			this.errors.cityState = "Please input a valid city and state";
			return false;
		}
		
		return true;
	};
	
	this.hasError = function(type) {
		return this.errors[type] !== null;
	};
	
	this.resetErrors = function() {
		this.errors = {
			cityState: null,
			username: null,
			password: null,
			terms: null
		};
	};
	
}