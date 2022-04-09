class FeRequestManager {
    beRequestHanderPath;

    constructor(beRequestHanderPath) {
        this.beRequestHanderPath = beRequestHanderPath;
    }

    request = async (getDatas = {}, postDatas = {}) => {
        const postUrlParams = new URLSearchParams();
        for(let dataKey in postDatas) {
            postUrlParams.append(dataKey, postDatas[dataKey]);
        }

        let getUrlParams = "";
        let getUrlParamCount = 0;
        for(let dataKey in getDatas) {
            getUrlParams += (getUrlParamCount == 0) ? '?' : '&';
            getUrlParams += `${dataKey}=${getDatas[dataKey]}`;
            getUrlParamCount++;
        }

        let respond = await fetch(this.beRequestHanderPath+getUrlParams, {
            method: "POST",
            body: postUrlParams
        });

        let jsonRespond = await respond.json();
        return jsonRespond;
    }
}

class ContentManager {
    feRequestManager;
    mainContainer;

    constructor() {
        this.feRequestManager = new FeRequestManager("./php/requestHandler.php");
        this.mainContainer = document.getElementById("mainContainer");
        this.loadContent("start");
    }

    loadContent = async(keyword) => {
        let respond = await this.feRequestManager.request({task: "getContent", keyword: keyword});
        if(respond.result == "ok") {
            this.mainContainer.innerHTML = respond.content;
            this.callFunctions(respond.functions);
        } else if(respond.result == "error") {
            alert(this.concatErrors(respond.errors));
        }
    }

    callFunctions = (functions) => {
        for(let func of functions) {
            this[func]();
        }
    }

    concatErrors = (errors) => {
        let errorMsg = "";
        let errorCount = 0;
        for(let msg of errors) {
            if(errorCount != 0) {
                errorMsg += "\r\n";
            }
            errorMsg += msgKeys[msg];
            errorCount++;
        }
        return errorMsg;
    }

    setUserDatas = async () => {
        const usernameSpan = document.getElementById("usernameSpan");
        const emailSpan = document.getElementById("emailSpan");
        const logoutLink = document.getElementById("logoutLink");

        let respond = await this.feRequestManager.request({task: "getUserData"});
        if(respond.result == "ok") {
            usernameSpan.innerHTML = respond.username;
            emailSpan.innerHTML = respond.email;
            logoutLink.onclick = async (e) => {
                e.preventDefault();
                let logOut = await this.feRequestManager.request({task: "logOut"});
                if(logOut.result == "ok") {
                    this.loadContent("start");
                } else if(logOut.result == "error") {
                    alert(this.concatErrors(logOut.errors));
                }
            }
        } else if(respond.result == "error") {
            alert(this.concatErrors(respond.errors));
        }
    }

    setLoginPage = () => {
        const inputElements = document.getElementsByTagName("input");

        for(let element of inputElements) {
            element.onkeyup = this.validateLoginForm;
        }

        document.getElementById("submitBtn").onclick = async () => {
            let respond = await this.feRequestManager.request(
                {task: "login"},
                {
                    userOrEmail: document.querySelector("#userOrEmailDiv input").value,
                    password: document.querySelector("#passwordDiv input").value
                }
            );

            if(respond.result == "ok") {
                this.loadContent("start");
            } else if(respond.result == "error") {
                alert(this.concatErrors(respond.errors));
            }
        }

        document.querySelector("#registrateLink a").onclick = (e) => {
            e.preventDefault();
            this.loadContent("registrate");
        }

        this.validateLoginForm();
    }

    validateLoginForm = () => {
        // userOrEmailDiv, passwordDiv, submitBtn
        const userOrEmailInput = document.querySelector("#userOrEmailDiv input");
        const passwordInput = document.querySelector("#passwordDiv input");
        const submitBtn = document.getElementById("submitBtn");

        let isFormValid = true;

        if(userOrEmailInput.value.length == 0) {
            this.setFeedback("userOrEmailDiv", false, "No username or e-mail address given!");
            isFormValid = false;
        } else if(userOrEmailInput.value.length < 5) {
            this.setFeedback("userOrEmailDiv", false, "Username or e-mail is lower than 5 characters!");
            isFormValid = false;
        } else if(userOrEmailInput.value.length > 40) {
            this.setFeedback("userOrEmailDiv", false, "Username or e-mail is higher than 40 characters!");
            isFormValid = false;
        } else {
            this.setFeedback("userOrEmailDiv", true, "");
        }

        if(passwordInput.value.length == 0) {
            this.setFeedback("passwordDiv", false, "No password given!");
            isFormValid = false;
        } else if(passwordInput.value.length < 8) {
            this.setFeedback("passwordDiv", false, "Password is lower than 8 characters!");
            isFormValid = false;
        } else if(passwordInput.value.length > 64) {
            this.setFeedback("passwordDiv", false, "Password is higher than 64 characters!");
            isFormValid = false;
        } else {
            const containsErrorText = this.checkPassword(passwordInput.value);
            if(containsErrorText.length == 0) {
                this.setFeedback("passwordDiv", true, "");
            } else {
                this.setFeedback("passwordDiv", false, containsErrorText);
                isFormValid = false;
            }
        }

        submitBtn.disabled = !isFormValid;
    }

    setRegPage = () => {
        const inputElements = document.getElementsByTagName("input");

        for(let element of inputElements) {
            element.onkeyup = this.validateRegForm;
        }

        document.getElementById("submitBtn").onclick = async () => {
            let respond = await this.feRequestManager.request(
                {task: "reg"},
                {
                    username: document.querySelector("#usernameDiv input").value,
                    email: document.querySelector("#emailDiv input").value,
                    pwd1: document.querySelector("#password1Div input").value,
                    pwd2: document.querySelector("#password2Div input").value
                }
            );

            if(respond.result == "ok") {
                alert("Registration is completed!\r\nNow, you can log in.");
                this.loadContent("start");
            } else if(respond.result == "error") {
                alert(this.concatErrors(respond.errors));
            }
        }

        document.querySelector("#loginLink a").onclick = (e) => {
            e.preventDefault();
            this.loadContent("start");
        }

        this.validateRegForm();
    }

    validateRegForm = () => {
        const usernameInput = document.querySelector("#usernameDiv input");
        const emailInput = document.querySelector("#emailDiv input");
        const password1Input = document.querySelector("#password1Div input");
        const password2Input = document.querySelector("#password2Div input");
        const submitBtn = document.getElementById("submitBtn");

        let isFormValid = true;

        if(usernameInput.value.length == 0) {
            this.setFeedback("usernameDiv", false, "No username given!");
            isFormValid = false;
        } else if(usernameInput.value.length < 5) {
            this.setFeedback("usernameDiv", false, "Username is lower than 5 characters!");
            isFormValid = false;
        } else if(usernameInput.value.length > 12) {
            this.setFeedback("usernameDiv", false, "Username is higher than 12 characters!");
            isFormValid = false;
        } else {
            this.setFeedback("usernameDiv", true, "Correct");
        }

        if(emailInput.value.length == 0) {
            this.setFeedback("emailDiv", false, "No e-mail address given!");
            isFormValid = false;
        } else if(!this.validateEmail(emailInput.value)) {
            this.setFeedback("emailDiv", false, "The e-mail address format is not correct!");
            isFormValid = false;
        } else if(emailInput.value.length > 40) {
            this.setFeedback("emailDiv", false, "e-mail address is higher than 40 characters!");
            isFormValid = false;
        } else {
            this.setFeedback("emailDiv", true, "Correct");
        }

        if(password1Input.value.length == 0) {
            this.setFeedback("password1Div", false, "No password given!");
            isFormValid = false;
        } else if(password1Input.value.length < 8) {
            this.setFeedback("password1Div", false, "Password is lower than 8 characters!");
            isFormValid = false;
        } else if(password1Input.value.length > 64) {
            this.setFeedback("password1Div", false, "Password is higher than 64 characters!");
            isFormValid = false;
        } else {
            const containsErrorText = this.checkPassword(password1Input.value);
            if(containsErrorText.length == 0) {
                this.setFeedback("password1Div", true, "Correct");
            } else {
                this.setFeedback("password1Div", false, containsErrorText);
                isFormValid = false;
            }
        }

        if(password2Input.value.length == 0) {
            this.setFeedback("password2Div", false, "No password confirmation given!");
            isFormValid = false;
        } else if(password2Input.value != password1Input.value) {
            this.setFeedback("password2Div", false, "Password confirmation is not equal to password!");
            isFormValid = false;
        } else {
            this.setFeedback("password2Div", true, "Correct");
        }

        submitBtn.disabled = !isFormValid;
    }

    setFeedback = (inputDivId, correct, feedbackText) => {
        const feedbackParagraph = document.querySelector(`#${inputDivId} p`);
        if(correct) {
            feedbackParagraph.classList.add("correct");
            feedbackParagraph.classList.remove("incorrect");
        } else {
            feedbackParagraph.classList.add("incorrect");
            feedbackParagraph.classList.remove("correct");
        }
        feedbackParagraph.innerHTML = feedbackText;
    }

    checkPassword = (password) => {
        const number = /[0-9]/;
        const lowercase = /[a-z]/;
        const uppercase = /[A-Z]/;

        let errorMsg = [];

        if(!number.test(password)) {
            errorMsg.push("number");
        }

        if(!lowercase.test(password)) {
            errorMsg.push("lowercase");
        }
        
        if(!uppercase.test(password)) {
            errorMsg.push("uppercase");
        }

        let errorMsgText = "";
        if(errorMsg.length > 0) {
            errorMsgText = "Password not contains"
            for(let i in errorMsg) {
                errorMsgText += (i != 0) ? ", " : " ";
                errorMsgText += errorMsg[i];
            }
            errorMsgText += ".";
        }
        return errorMsgText;
    }

    validateEmail = (email) => {
        return String(email).toLowerCase().match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    }
}

const contentManager = new ContentManager();