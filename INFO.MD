### What is uses

- KnpLabs/KnpMenuBundle

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

### How it works

1. Everything starts from `/admin` path
2. `AdminController` invoked and renders `dashboard` template
3. Next events are dispatched to build dashboard:
    - `menu.admin_top_menu` - to build top menu
    - `menu.admin_sidebar_menu` - to build sidebar menu
4. After collecting all required data — dashboard is rendered
