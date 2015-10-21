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
	this.usernameAvailable = false;
	this.creationResponse = {};
	
	var stage;
	
	this.goBack = function() {
		stage = 0;
	};
	
	this.isOnStage = function(checkStage) {
		return stage === checkStage;
	};
	
	this.isPastStage = function(checkStage) {
		return stage > checkStage;
	};
	
	this.open = function() {
		ctrl.busy = true;
		
		stage = 0;
		
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
		
		if(stage === 0) {
			if(this.fields.city == "" || this.fields.state == "") {
				return true;
			}
		} else if(stage === 1) {
			if(this.fields.username == "") {
				return true;
			}
		} else if(stage === 2) {
			if(this.fields.password1 == "" || this.fields.password2 == "") {
				return true;
			}
		} else if(stage === 3) {
			if(!this.fields.terms) {
				return true;
			}
		}
		
		return false;
	};
	
	this.submit = function() {
		this.resetErrors();
		
		var returnType = true;
		
		if(stage === 0) {
			if(!this.validateDob()) {
				returnType = false;
				this.errors.dob = "Please input a valid Date of Birth";
			}
			if(!this.validateCountry()) {
				returnType = false;
				this.errors.country = "Please select a valid Country";
			}
			
			if(returnType) {
				stage = 1;
			}
		} else if(stage === 1) {
			this.validateUsername(true);
		} else if(stage === 2) {
			var passwordReturn = this.validatePasswords();
			if(passwordReturn === -1) {
				stage = 3;
			} else {
				this.errors.password = this.passwordErrors[passwordReturn];
			}
		} else if(stage === 3) {
			if(this.validateTerms()) {
				this.creationResponse = {
					title: "Submission in Progress", 
					message: "Your information is being submitted, please wait...",
					success: true
				};
				stage = 4;
				
				ctrl.busy = true;
				
				var thisObj = this;
				service.submitAccountCreation(this.fields, function(returnCode) {
					if(returnCode == "-1") {
						thisObj.creationResponse = {
							title: "Account Creation Complete", 
							message: "Your user account has been successfully created with the username and password you have chosen.",
							success: true
						};
					} else {
						thisObj.creationResponse = {
							title: "An Error has Occurred", 
							message: "TODO: message from server",
							success: false
						};
					}
					
					ctrl.busy = false;
				});
			}
		}
	};
	
	this.validateTerms = function() {
		if(!this.fields.terms) {
			this.errors.terms = "You must agree to both the Terms of Use and Privacy Policy in order to create an account.";
			return false;
		}
		
		return true;
	};
	
	this.validatePasswords = function() {
		this.fields.password1 = this.fields.password1.trim();
		if(this.fields.password1 === "") {
			return 0;
		}
		
		if(this.fields.password1.length < 5 || this.fields.password1.length > 35) {
			return 1;
		}
		
		if(!this.fields.password1.match(/^[A-Za-z0-9]{5,35}$/)) {
			return 2;
		}
		
		this.fields.password2 = this.fields.password2.trim();
		if(this.fields.password2 === "" || this.fields.password2 !== this.fields.password1) {
			return 3;
		}
		
		return -1;
	};
	
	this.usernameChanged = function() {
		this.usernameAvailable = false;
	};
	
	this.validateUsername = function(changeStage) {
		if(!changeStage) {
			this.resetErrors();
		}
		
		this.usernameAvailable = false;
		
		this.fields.username = this.fields.username.trim();
		if(this.fields.username === "") {
			this.errors.username = this.usernameErrors[0];
			return;
		}
		
		if(this.fields.username.length < 1 || this.fields.username.length > 15) {
			this.errors.username = this.usernameErrors[1];
			return;
		}
		
		if(!this.fields.username.match(/^[A-Za-z0-9 ]{1,15}$/)) {
			this.errors.username = this.usernameErrors[2];
			return;
		}
		
		ctrl.busy = true;
		
		var thisObj = this;
		service.checkUsername(this.fields.username, function(returnCode) {
			if(returnCode == "-1") {
				thisObj.usernameAvailable = true;
				if(changeStage) {
					stage = 2;
				}
			} else {
				thisObj.errors.username = thisObj.usernameErrors[parseInt(returnCode)];
			}
			
			ctrl.busy = false;
		});
	};
	
	this.validateCity = function() {
		
	};
	
	this.validateState = function() {
		
	};
	
	this.validateDob = function() {
		this.fields.dob = this.fields.dob.trim();
		if(this.fields.dob === "") {
			return false;
		}
		
		if(this.fields.dob.length !== 10) {
			return false;
		}
		
		var day = -1;
		var month = -1;
		var year = -1;
		
		try {
			day = parseInt(this.fields.dob.substr(0, 2));
			month = parseInt(this.fields.dob.substr(3, 2));
			year = parseInt(this.fields.dob.substr(6, 4));
		} catch(error) {
			return false;
		}
		
		if(isNaN(day) || isNaN(month) || isNaN(year)) {
			return false;
		}
		
		if(day < 1 || day > 31) {
			return false;
		}
		if(month < 1 || month > 12) {
			return false;
		}
		if(year < 1900 || year > 2016) {
			return false;
		}
		
		// TODO: MOAR!
		
		return true;
	};
	
	this.hasError = function(type) {
		return this.errors[type] !== null;
	};
	
	this.resetErrors = function() {
		this.errors = {
			dob: null,
			cityState: null,
			username: null,
			terms: null,
			password: null
		};
	};
	
}

function compare(a,b) {
    return parseInt(a) - parseInt(b);
}