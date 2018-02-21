Setup:

```
composer require tobur/gedmo-translation-form
```

Add to config parameters:

```
locales:
  - ua
  - en
default_locale: ua
```

To twig:

```
form_themes:
        - 'Form/translatable.html.twig'
```