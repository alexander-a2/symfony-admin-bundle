# Symfony Admin Bundle

### Easy and customizable admin panel for symfony 

---

![Entity list](/docs/images/entity-crud-list.png)

### Easy installation

1. Add package to composer
```bash
$ composer require alexander-a2/symfony-admin-bundle
```

2. Add routes to route file `./config/routes.yaml`:

```yaml
admin: # any name could be used 
    resource: "@AdminBundle/Resources/config/routing.yml"
    prefix: /admin # any preferred path to admin panel
```

### CRUD Example

![Entity list](/docs/images/entity-crud-list.png)
![Entity list](/docs/images/entity-crud-view.png)
![Entity list](/docs/images/entity-crud-edit.png)

