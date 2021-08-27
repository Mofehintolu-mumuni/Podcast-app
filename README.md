# podchaser-app
This application shows how a command line application can be used to process podcasts and episodes via an rss feed url. It was developed using laravel.

There is an in-app sql lite database setup for testing.

# Application setup

This application is running on laravel version ^8.54, and PHP ^8.0. Kindly ensure that your system meets this specification.

Ensure that your .env file is an exact replica of the .env.example file.

The main business logic of this application comprises of an artisan command that can be accessed by running the follwoing command: vendor/bin/sail artisan process:podcast. The process:podcast is an interactive command that can receive one or more rss feed urls, process podcast xml data and save in a database. The command also has a progress bar that keeps track of the status this was done by harnessing laravel batch job functionality and artisan command progress bar functionality. The progress bar keeps track of the status of jobs processed in a given batch.

The main business logic that handles the processing of rss feeds in encapsulated in a service called ProcessPodcastService to  promote maintainability.

A job is dispatched for each rss feed url received via the process:podcast interactive command.

Error handling was implemented in the application with errors logged via the Illuminate\Support\Facades\Log  Facade.



  # Podchaser App Setup
  This application has the following server app setup
  
  Run composer install

  To publish Sail's docker-compose.yml run:  php artisan sail:install

  To start sail environment run command: vendor/bin/sail up

  To run migration files run command: vendor/bin/sail artisan migrate

  To run the podcast artisan command run command: vendor/bin/sail artisan process:podcast

  To run unit tests use the following command: vendor/bin/sail artisan test