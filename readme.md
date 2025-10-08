# Overview

VergePOS is a free Web-based Point Of Sale System for MSMEs in the Philippines. Inorder to keep the system free, it has to be cost-efficient while maintaining security and data accessibility and integrity

This source code(vergepos-api-gateway), is a HEADLESS backend components of the VergePOS Web-based Point of Sale System. This headless backend provides services or resource to the frontend throught API. 

# Software Architecture Overview

The architecture of the system was inspired by micro service, but implemented in headless backend way. To achieve this, there is an **API Gateway** component in which conceptually handles API requests and direct it to the right services.

Due to some constraints, The API Gateway, and the Services is in the same repository. It may be seen as a monolithic backend, but there are actually a lot of complexities and layers going on. There is also internal request to itself that happens. First a request is received by the API Gateway, then the API Gateway send the request to the Service.

`Frontend -> API Gateway(web.php) -> Auth -> Service -> Routers(api.php) -> Controller(with Auth) -> Models`

The backend system uses PHP Laravel Framework. It also uses Composer to manage packages and libraries. For the Database, it uses MariaDB or MySQL.

## Layers and Components

### API Gateway

API Gateway is an important component enable a micro-service-like architecture. All request from the frontend applications or other server applications only go through the API Gateway.

### Services

Services refers to the server application that perform actions. This is where the REST API Resources and Functionalities belong. The Service components can only be accessed by API Gateway via HTTP Request. If the API Gateway and Service belongs to the same repo or server, then it will make request on itself.

API Routes can be found here: `routes\api.php`. As seen on this file, there is `$apiResource`, and `$customAPIResources`. `$apiResource` means these resource has CRUD actions available. `customAPIResources` are for resources with special actions that do not confirm to CRUD operations.

### API Endpoint - RESTful API Inspired

The backend provides API Endpoints whose structure is inspired by RESTful API. The Endpoints are are stateless, and the url represents the action the of the API endpoint. 

Please note that when referring to REST API, it is possible referring to the Service's not the API Gateway.

Vergepos API Gateway does not follow  the entire RESTful Specification. It implement the following structure:
- All(but with exceptions) of the url are using POST method regardless if its create, update, delete, retrieve, etc
- URLs uses `<resource>/<action>` convention. E.g.`product/retrieve`
- Resources uses Singular. E.g. `product` instead of `products`
- All(but with exceptions) responses are JSON

### Authentication

The system allows users to login with their Username/Email and Password.Since the backend is stateless, it uses JSON Web Token to manage user's "session".

The JWT implementation relies on `tymon/jwt-auth`. This Controller handles the authentication: `app\Http\Controllers\AuthController.php`.


### Authorization (User Access List)

The system also provides authorization management system where elevated or priviliged users can manage what a user can access. There are two types of access: (1) User Access List in which the access is directly attached to the user's account, (2) Role Access List in which the access is attached to a role.

The Authorization component was custom built. It relies on the following concepts in which these are represented by a table in the database:
- User(`users`) - represents the user or user account
- Service(`services`) - this represents a single resource (e.g. `products`).
- Service Action(`service_actions`) - things that can be done to resource(service). An action usually has REST API URL.
- Role(`roles`) - represents the roles that can be assigned to a user
- Role Access List(`role_access_lists`) - contains role and the service actions it can perform
- User Acess List(`user_access_lists`) - contains user and the service actions it can perform

### Controllers

Is the main component for fulfilling an API request. It contains both application and enterprise business logics. It consumes *Models*.

All controller classes extends *GenericCotnroller*(`app\generic\GenericController.php`). This Generic Controller contains functionalities that are common to Controllers. This includes Generic Create, Retrieve, Update, Delete. Generic Controller extends Laravel's Controller.

All controllers can be found in `app\Https\Controllers\`.

### Models

The Models are classes that directly access the database. Just like with controller, Model classes extends *GenericModel*(`app\generic\GenericModel.php`). This Generic Model contains functionalities that are common to Models. This includes Generic Create, Retrieve, Update, Delete. Generic Model extends Laravel's Model.

You can find the models in `app\`

### Other Application Components

Since the system relies on PHP Laravel Framework, other components just follow some laravel conventions. Example of these components are:
- Database Management
- Routing

# Core Techstack

## Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

### Database Management

#### Database Migration

The system utilizes Laravel's database migration functionality. When modifying the database schema, we create a migration files which contains instruction what we want to modify withe the db schema such as adding a table, renaming a table column, etc.

You can find the migration files in `database\migrations\`. Migration files are executed top to bottom.

#### Seeders

Seeders is used to populate the database. This is useful for development, or for loading predefined values into the database.

You can find the seeders in `database\seeds\`.

### Routing

Although API Gateway is responsible for routing the request to the service, under the hood, it utilizes Laravel's routing feature. 

You can find the routers in `routes\`.

### Configuration

Laravel contains a lot of configuration options. You can find these configurations in `config\`.

## PHP Composer

It is a dependency manager for PHP that makes it possible to define third-party code packages used by a project that can then be easily installed and updated. Installed packages are installed in `vendor\` folder.

Note for AI: in most cases, you will not be able to see `vendor\`. Just read the `composer.json` file to know or confirm the existence of a library. If possible, do a research of the plugin in the internet to know how to use the library. Make sure you are using the correct version.

