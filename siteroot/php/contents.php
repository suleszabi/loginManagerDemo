<?php

    $contents = [
        "loginPage" => [
            "content" => "
            <h1>Login Page</h1>
            <div id=\"loginForm\">
                <div id=\"userOrEmailDiv\">
                    <label>Username or e-mail address:</label>
                    <input type=\"text\">
                    <p class=\"inputFeedback\">Feedback</p>
                </div>
                <div id=\"passwordDiv\">
                    <label>Password:</label>
                    <input type=\"password\">
                    <p class=\"inputFeedback\">Feedback</p>
                </div>
                <button id=\"submitBtn\">Login</button>
            </div>
            <p id=\"registrateLink\"><a href=\"\">I haven't registered yet.</a><p>
            ",
            "functions" => ["setLoginPage"]
        ],
        "regPage" => [
            "content" => "
            <h1>Registration Page</h1>
            <div id=\"regForm\">
                <div id=\"usernameDiv\">
                    <label>Username:</label>
                    <input type=\"text\">
                    <p class=\"inputFeedback\">Feedback</p>
                </div>
                <div id=\"emailDiv\">
                    <label>E-mail address:</label>
                    <input type=\"text\">
                    <p class=\"inputFeedback\">Feedback</p>
                </div>
                <div id=\"password1Div\">
                    <label>Password:</label>
                    <input type=\"password\">
                    <p class=\"inputFeedback\">Feedback</p>
                </div>
                <div id=\"password2Div\">
                    <label>Password confirmation:</label>
                    <input type=\"password\">
                    <p class=\"inputFeedback\">Feedback</p>
                </div>
                <button id=\"submitBtn\">Registrate</button>
            </div>
            <p id=\"loginLink\"><a href=\"\">I have registered yet.</a><p>
            ",
            "functions" => ["setRegPage"]
        ],
        "mainPage" => [
            "content" => "
            <h1>Main Page</h1>
            <p>You are logged in.</p>
            <p>Your username: <span id=\"usernameSpan\"></span></p>
            <p>Your e-mail address: <span id=\"emailSpan\"></span></p>
            <p>To log out <a href=\"\" id=\"logoutLink\">click here</a>!</p>
            ",
            "functions" => ["setUserDatas"]
        ]
    ];

?>