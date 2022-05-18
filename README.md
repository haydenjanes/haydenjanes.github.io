# Just a Contact Form

This repository provides a simple HTML and AJAX form to demonstrate the usage of your own from backend. It can be used on static websites, e.g., github pages, and post JSON data to your own backend server using [Cross-Origin Resource Sharing](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS).

It sends the form data via AJAX using [jQuery](https://jquery.com/) and [jQuery Validation Plugin](https://github.com/jquery-validation/jquery-validation/) to the [backend](https://gitlab.com/just-code/just_contact_backend).

You can see a demo and share your thought by using [this project`s GitLab page](https://www.just-code.io).

The [helper script](hide-mail.html) is used to encode the fallback email which will be visible on screen after user solves captcha and server has an error. We still recommend to encode the mail for spambots parsing javascript code.
