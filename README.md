# Homeowner Names - Technical Test


![Screenshot 2025-05-18 182954](https://github.com/user-attachments/assets/faa5505d-13c1-47e2-8ba5-c8ecbd5e943e)
![Screenshot 2025-05-18 182926](https://github.com/user-attachments/assets/cae1045f-8ffd-449a-baf2-342535251d6b)

## Setup and Requirements
This has been written using PHP8.4 within Sail. 
To set up run: 

```sh
composer install
sail up -d
```

From there you should be able to navigate to `localhost:80` to view the web interface 

## Using application

The web compnent has a simple file uploader, and will display the latest upload as well as recent uploads in a table.
(Screenshot above)

There is also a CLI command
```sh
sail artisan import:people {filename}
```

## Testing

There are some tests. By default they use a seperate database within Sail, to set this up run:

```sh
sail mysql
create database test
```
The name of the database can be found within phpunit.xml

Once done you should be free to run:

```sh
sail artisan test
```


# Homeowner Names - Technical Test
> Please do not spend too long on this test, 2 hours should be more than sufficient. You may
choose to create a full application with a basic front-end to upload the CSV, or a simple class
that loads the CSV from the filesystem.

You have been provided with a CSV from an estate agent containing an export of their
homeowner data. If there are multiple homeowners, the estate agent has been entering both
people into one field, often in different formats.

Our system stores person data as individual person records with the following schema:

### Person

- title - required
- first_name - optional
- initial - optional
- last_name - required

Write a program that can accept the CSV and output an array of people, splitting the name into
the correct fields, and splitting multiple people from one string where appropriate.

For example, the string “Mr & Mrs Smith” would be split into 2 people.

## Example Outputs

Input
`“Mr John Smith”`

Output
```
$person[‘title’] => ‘Mr’,
$person[‘first_name’] => “John”,
$person[‘initial’] => null,
$person[‘last_name’] => “Smith”
```

Input
`“Mr and Mrs Smith”`

Output
```
$person[‘title’] => ‘Mr’,
$person[‘first_name’] => null,
$person[‘initial’] => null,
$person[‘last_name’] => “Smith”
$person[‘title’] => ‘Mrs’,
$person[‘first_name’] => null,
$person[‘initial’] => null,
$person[‘last_name’] => “Smith”
```

Input
`“Mr J. Smith”`

Output
```
$person[‘title’] => ‘Mr’,
$person[‘first_name’] => null,
$person[‘initial’] => “J”,
$person[‘last_name’] => “Smith”
```
