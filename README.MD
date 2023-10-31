# Symfony Admin Bundle

## Installation

1. Register bundle

add line
`AlexanderA2\SymfonyAdminBundle\AdminBundle::class => ['dev' => true],`
to file
`config/bundles.php`

2. import routes

add lines: 
`admin:
    resource: '@AdminBundle/Resources/config/routing.yml'`
to file
`config/routes.yaml`