let $ = jQuery

class AuthToken {
    constructor(redirectLink, username, password, email) {
        this.username = username
        this.password = password
        this.email = email
        this.redirectLink = redirectLink
        this.events()
    }

    events() {
        let formData = {
            username: this.username,
            email: this.email,
            password: this.password
        }
        // erase existing cookies 
        this.eraseCookie('inpiryAuthToken')

        let url = `${inspiryData.root_url}/wp-json/jwt-auth/v1/token`

        // set auth cookies 
        fetch(url, {
            method: "POST",
            body: JSON.stringify(formData),
            headers: {
                'Content-Type': 'application/json'
            },
        })
            .then(res => res.json())
            .then(res => {
                // document.forms["login-form"].submit();
                if (res.data) {
                    console.log(res.data.status)
                }
                else {
                    this.setCookie('inpiryAuthToken', res.token, 3)
                    if (this.redirectLink) {
                        window.location.replace(this.redirectLink);
                    }
                    else {
                        window.location.replace("/");
                    }

                }
            })
            .catch(err => console.log(err))

    }

    setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    eraseCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
}
export default AuthToken