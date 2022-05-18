# Just a PHP backend to be used with static websites

This project just demonstrates PHP code that receive a message from a static website, e.g., a jekyll project hosted on [GitLab pages](https://docs.gitlab.com/ee/user/project/pages/).

The backend communicates to [the frontend](https://gitlab.com/just-code/just_contact_frontend) using the [Cross Origin Ressource Sharing](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS). The scripts checks for CORS Headers and performs a basic validation than forwards the message with [PHPMailer](https://github.com/PHPMailer/PHPMailer) using local mail server or an external SMTP client.

## Configuration

If you prefer an ansible installation of this backend, have a look [here](https://gitlab.com/just-code/just_ansible).

If you want to deploy this project to an existing backend, just edit the following files:

* [http.ini](backend/src/http.ini) for setting http restrictions
* [mail.ini](backend/src/mail.ini) for setting up PHPMailer
* [.htaccess](backend/src/.htaccess) for setting CORS header Access-Control-Allow-Origin for apache2

In case you run nginx with we need to add CORS headers. The config should look like this:

```
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        add_header Access-Control-Allow-Origin "https://enter-your-frontend.website" always;
        add_header Access-Control-Allow-Credentials "true" always;
        add_header Access-Control-Request-Method "POST" always;
        add_header Access-Control-Allow-Headers "*" always;
        add_header Content-Disposition "attachment" always;
        add_header X-Content-Type-Options "nosniff" always;

    }
```

We run php-fpm on port 9000. In case you connect with php over unix socket, change this value.

On nginx, remember to hide the internal files and directories:
```
    location ~* (src|test|vendor|composer.lock|composer.json) {
        return 404 ;

    }
```

To use PHPMailer we use [composer](https://getcomposer.org/download/) as the dependency manager.

## Development

Update correct configuration

``` bash
cd backend
composer install
```

To run unitests you need [phpunit](https://phpunit.de/getting-started/phpunit-9.html) (already added in dependencies for development)

```bash
./vendor/bin/phpunit --bootstrap vendor/autoload.php test/
```

## Production

Update correct configuration

```bash
cd backend
composer install --no-dev
```

In case you installed all dependencies in development phase, just add `--no-dev` flag to automatically remove redundant packages that are not used for production.

### Run with Docker

You can run the backend locally in docker-compose:

```bash
docker-compose up
```

### Install with ansible

To make your life *just* easier we created a production ready [ansible playbook](https://gitlab.com/just-code/just_ansible). Clone the [the frontend](https://gitlab.com/just-code/just_contact_frontend) and deploy on GitHub or GitLab, create a new virtual server on AWS, netcup, OVH, starto. Configure the settings and have your new contact front and backend in less than half an hour!
