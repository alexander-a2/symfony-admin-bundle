# Symfony Admin Bundle

### Easy and customizable admin panel for symfony with:

- Responsive admin dashboard template, based on Bootstrap 5 with no extra styles
- All entities list with CRUD operations out of the box
- Filtering by all fields, out of the box

---

![Entity list](/docs/images/entity-crud-list.png)

### Easy installation

1. Add package to composer
```bash
$ composer require alexander-a2/symfony-admin-bundle
```

2. Add routes to route file `./config/routes.yaml`:

```yaml
admin: 
    resource: "@AdminBundle/Resources/config/routing.yml"
    prefix: /admin
    name_prefix: admin_

# override default admin index page
admin_index:
    path: /admin
    # set your controller::action here
    controller: App\Controller\Admin\AdminController::indexAction
```

### CRUD Example

![Entity list](/docs/images/entity-crud-list.png)
![Entity list](/docs/images/entity-crud-view.png)
![Entity list](/docs/images/entity-crud-edit.png)

