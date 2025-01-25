### What is uses

- doctrine/orm
- knplabs/knp-menu-bundle

### Installation

1. Add package to composer
```bash
$ composer require alexander-a2/symfony-admin-bundle
```

2. Add routes to route file:

`./config/routes.yaml`

```yaml
admin: # any name could be used 
    resource: "@AdminBundle/Resources/config/routing.yml"
    prefix: /admin # any preferred path to admin panel
```
