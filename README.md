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
twig:
  form_themes:
    - 'GedmoTranslationFormBundle:Form:translatable.html.twig'
```

How to use in Sonata "configureFormFields":

```
        $formMapper->add(
            'name',
            TranslationType::class,
            [
                'type' => TextType::class,
            ]
        );

        $formMapper->getFormBuilder()->setData($this->getSubject());
```

![How to look like](https://raw.githubusercontent.com/Tobur/gedmo-translation-form/master/how-to-lool-like.png)