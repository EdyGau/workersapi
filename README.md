# WorkersApi

WorkersApi provides a RESTful interface that allows you to manage employee data. You can use various HTTP queries like GET, POST, PUT, DELETE to read, track, update and delete module data.

# Installation

To use the command, run

    composer install

### How to generate fixtures: 

To generate fixtures

    php bin/console doctrine:fixtures:load

$$$ How to use

- `GET /api/workers`: support list of all employees.
- `GET /api/worker/{id}`: pay attention to information about the given employee based on his ID.
- `POST /api/worker`: Add a new employee based on the data sent.
- `PUT /api/worker/{id}`: Updates employee information based on employee ID and data upload.
- `DELETE /api/worker/{id}`: Deletes an employee based on his ID.

# Technologies Used

PHP

Symfony

Composer

Nelmio

JWT (JSON Web Token):
## Authors

- [@edygau](https://www.github.com/edygau)

