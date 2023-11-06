# Symfony Admin Bundle

## Installation

1.Register bundle

add lines
`AlexanderA2\SymfonyAdminBundle\AdminBundle::class => ['all' => true],
AlexanderA2\SymfonyDatasheetBundle\DatasheetBundle::class => ['all' => true],`
to file
`config/bundles.php`

2.import routes

add lines: 
`admin:
    resource: '@AdminBundle/Resources/config/routing.yml'`
to file
`config/routes.yaml`

3.Install assets

run
`bin/console asset:install`

4.Make symfony authentication

`bin/console make:user`
`bin/console make:auth`
`bin/console make:registration-form`

update db
`bin/console make:migration`
`bin/console doctrine:migrations:migrate`

## Useful tips:

1. Use STOF doctrine extensions:
`composer require stof/doctrine-extensions-bundle`
configure it in `config/packages/stof_doctrine_extensions.yaml`
`stof_doctrine_extensions:
    default_locale: en_US
    orm:
        default:
            timestampable: true
            tree: true`
(and use `use TimestampableEntity;` in almost every entity

2. Use bootstrap forms for twig