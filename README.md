# Simple dependency parser for .json and .lock files.

## Installation
```
git clone https://github.com/elirud/dependency_parser.git
```

## Requirements
Requirements to run Symfony applications can be found [here.](https://symfony.com/doc/4.2/reference/requirements.html)
Note that this command can only handle .json and .lock files.

## Running the command
To use the command, navigate to the dependency-parser directory and run
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
To run these:
```
php bin/phpunit
```
