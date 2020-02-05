# Simple dependency parser for .json and .lock files.

## Requirements
Requirements to run Symfony applications can be found [here.](https://symfony.com/doc/4.2/reference/requirements.html)
Note that this command can only handle .json and .lock files. You will also
need composer, which can be downloaded [here.](https://getcomposer.org/download/)

## Installation
Clone the repository by running git clone like so:
```
git clone https://github.com/elirud/dependency_parser.git
```
Then navigate to the dependency_parser directory and run:
```
composer install --prefer-source
```
to build files needed to run the command.

## Running the command
To use the command, navigate to the dependency_parser directory and run:
```
php bin/console app:parse-files
```
This will print a table for dependencies found in each .json and .lock file
in the .\\public\\files directory. So either put the files to be parsed here,
or if instead the files are already located elsewhere, simply add the path to
that directory using the --dir option like so:
```
php bin/console app:parse-files --dir [PATH TO DIRECTORY]
```

## Testing the command
This repository also has tests for different cases in the tests directory.
To run these, use phpunit like so:
```
php bin/phpunit
```
