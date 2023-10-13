# ONS Dave

## Overview
Dave is the Daily Attendance & Validation Engine, a Laravel application to allow employees to track their working hours and managers to validate them.

## Run Locally (Laravel Sail)

Clone the project

```bash
git clone https://github.com/angus-websites/ONSDave.git
```

Go to the project directory

```bash
cd ONSDave
```

Setup Laravel Sail

**_NOTE:_**  Ensure you have Docker installed

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

Generate a .env file

```bash
cp .env.example .env
```
Run Laravel Sail (Development server)

```bash
./vendor/bin/sail up
```

**Open a new Terminal tab in the same project root folder**

Generate an app encryption key

```bash
./vendor/bin/sail php artisan key:generate
```

Migrate the database

```bash
./vendor/bin/sail php artisan migrate
```

Install NPM dependancies

```bash
./vendor/bin/sail npm i
```

Run Vite

```bash
./vendor/bin/sail npm run dev
```

Visit [Localhost](http://localhost/)

## Configuration

| Variable Name      | Purpose                               | Accepted Values   | Default Value |
| ------------------ | ------------------------------------- | ----------------- | ------------- |
| SHOW_LOGIN_BUTTON  | Visibility of the login button         | true, false       | true          |
| DISABLE_REGISTRATION  | Should registration be disabled         | true, false       | false          |
| ADMIN_NAME         | Name of the admin user                 | String            | -             |
| ADMIN_EMAIL        | Email of the admin user                | String            | -             |
| ADMIN_PASSWORD     | Password for the admin user            | String            | -             |


## Tips

When updating certain fields in the `.env` file when using Laravel Sail, you may need to restart the Docker container for changes to take affect.

## CI/CD and Dockerization

This project includes configuration for a CI/CD pipeline using GitHub Actions and Docker. The `Dockerfile` defines a multi-stage build that separates the dependencies for production and testing, and optimizes the size of the final image.

The application server is configured using `nginx`, and the configuration file can be found at `nginx.conf`.

Files and directories that should be ignored by Docker are listed in `.dockerignore`.

The `start_prod.sh` script is used to start the application in a production environment, and `start_tests.sh` is used to run tests in the Docker container.

Workflows for Continuous Integration and Continuous Deployment are defined in the `.github/workflows` directory. There are separate workflows for running tests (`ci.yml`) and for building and pushing the Docker image to the GitHub Container Registry (`cd.yml`).

### GitHub Secrets

The CD workflow requires two GitHub secrets:

1. `GITHUB_TOKEN`: A token for authentication with the GitHub Container Registry. This token is automatically generated by GitHub. You don't need to manually generate it.

2. `WEBHOOK_URL`: The URL for a webhook that will be triggered after the Docker image is pushed to the container registry.

To add these secrets to your GitHub repository, go to your repository on GitHub, click on "Settings", then "Secrets", and add the secrets there.

Currently the Github workflows are set to only trigger manually, to setup an automated CI/CD pipeline, removed `workflow_dispatch: ` from `.github/workflows/ci.yml` and `.github/workflows/cd.yml` and uncomment the next few lines.

### Server Configuration

After the Docker image is pushed to the container registry, you will need to pull the image on your server and restart your application. This process will depend on your server setup. 

When running the Docker container, it is important to inject your environment variables at runtime. The Dockerfile and start scripts are set up to generate an `.env` file from the environment variables in the Docker container. This is done by running the command `printenv | awk -F "=" 'NF==2 && $2 !~ /[\n\t ]/' > .env` at the start of the script. 

Ensure that your Docker run command includes the `-e` option to set the environment variables, for example:

```bash
docker run -d -p 80:80 --name my-app \
    -e APP_NAME=MyApp \
    -e APP_ENV=production \
    -e APP_KEY=my-app-key \
    # Add all other necessary environment variables here...
    my-app-image
```

**_NOTE:_**  The above can normally be automated using a server management tool such as [CapRover](https://caprover.com/), [EasyPanel](https://easypanel.io/) etc.

#### Volume mounting

In a Dockerized environment, volume mounting is often used to ensure that certain data persists beyond the life of a container or to share data between the host and container. In the case of LaraVellous, you may want to volume mount the storage folder to ensure that any uploaded files, logs, or other persistent data are kept intact across container restarts or rebuilds.

```bash
docker run -d -p 3000:80 --name laravellous \
    -v /path/to/laravellous/storage:/var/www/html/storage \
    # Other env variables and options etc...
    laravellous-prod-image

```

## Building and Running Docker Images Locally

For development and troubleshooting, Laravel Sail is generally recommended. However, you can also build and run the Docker images locally, especially when testing changes or finalizing your production setup.

### Building the Docker Image

You can build a Docker image of your application using the `docker build` command. Be sure to specify the target stage in the Dockerfile and tag the image. The following command builds the image for the `test` stage and tags the image as `laravellous-test-image`:

```bash
docker build --target test -t laravellous-test-image . # Build the testing image
docker build --target prod -t laravellous-prod-image . # Build the production image (will start nginx, php-fpm etc)
```

**_NOTE:_** Running docker build with no target specified will produce an image that is not optimized for it's environment and may cause unexpected behavior

### Running the Docker Image

Once the Docker image is built, you can run it using the `docker run` command. This command creates and starts a new container for your image. Remember to specify the environment variables at runtime, as per the [Server Configuration](#server-configuration) section.

Here is an example of how to run the image:

Testing

```bash
docker run --name laravellous-tests laravellous-test-image # Run the testing image (this will execute the artisan test suite)
```

Production

Add the following lines in the `start_prod.sh` file (line 13), remember to remove them before adding to a production server

```bash
touch /var/www/html/database/database.sqlite
chmod -R 777 /var/www/html/database/database.sqlite
```

Then run this docker run command:

```bash
docker run -d -p 3000:80 --name laravellous \
    -e APP_NAME=LaraVellous \
    -e APP_ENV=production \
    -e APP_KEY=base64:NHZpNnVnM2p0b2VmZnV6MDN1ZDJmeWt1bDJpemlxeDA= \
    -e DB_CONNECTION=sqlite_testing \
    -e ADMIN_NAME=Bob \
    -e ADMIN_EMAIL=bob@gmail.com \
    -e ADMIN_PASSWORD=password \
    laravellous-prod-image
```

**_NOTE:_** This prod script will copy **ALL** the environment variables in the current that do not contain spaces or newlines

In the command above:

- `-d` runs the container in the background
- `-p` maps port 3000 on your machine to port 80 on the container
- `--name` sets a name for the container
- `-e` sets the environment variables

With this setup, your application will be accessible at `http://localhost:3000`.


**_NOTE:_**  It's important to note that Laravel Sail is more suitable for development and troubleshooting due to its preconfigured environment and tools. However, understanding how to build and run your application with Docker directly can be helpful, especially when you are moving towards deployment.


## Authors

- [@angusgoody](https://github.com/angusgoody)

