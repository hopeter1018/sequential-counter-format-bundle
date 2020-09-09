# sequential-counter-format-bundle

## Introduction

This bundle aims to generate value for doctrine entities.

## Installation

### Require the package

`composer require hopeter1018/sequential-counter-format-bundle`

### Add to kernel

#### Symfony 4+ or Symfony Flex

Add `/config/bundles.php`

```php
return [
  ...,
  HoPeter1018\SequentialCounterFormatBundle\HoPeter1018SequentialCounterFormatBundle::class => ['all' => true],
];
```

#### Symfony 2+

Add `/app/AppKernel.php`

```php
$bundles = [
  ...,
  new HoPeter1018\SequentialCounterFormatBundle\HoPeter1018SequentialCounterFormatBundle(),
];
```

### Config

#### thru config.yml / config.yaml

```yml
hopeter1018_sequential_counter_format:
  # managers: ['default'] # add / modify if you are not using 'default'
  rules:
    _rule_alias_:
        entity_class: 'Your\Bundle\Entity\EntityClass'
        property: 'yamlTargetPropertyName'
        format: '__SEE_FORMAT_SECTION__'
```

#### thru @YourBundle/Resources/config/sequential_counter_format.yaml

```yml
Your\Bundle\Entity\EntityClass:
  attributes:
    yamlTargetPropertyName:
      format: "__SEE_FORMAT_SECTION__"
      batchPrefix: ""
```

#### thru Doctrine Annotation

```php
namespace Your\Bundle\Entity;

use HoPeter1018\SequentialCounterFormatBundle\Annotations as Scf;

/**
 * Class docblock
 *
 * @Scf\ClassRule(settings={
 *   "classAnnotationTargetPropertyName"={"format"="__SEE_FORMAT_SECTION__", "batchPrefix"="[site.id]"},
 * })
 */
class EntityClass {

  /**
   * @Scf\PropertyRule("__SEE_FORMAT_SECTION__")
   * @ORM\Column(type="string", length=255)
   */
  private $propertyAnnotationTargetPropertyName;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $classAnnotationTargetPropertyName;

  /**
   * declared in the above section: `thru config.yml / config.yaml`
   */
  private $yamlTargetPropertyName;

}
```

## Usage

### Format Placeholder

| Type     | Format                       | Description                                                                                                                                                                                                                        |
| -------- | ---------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Sequence | %d                           | same as [sprintf](<>)<br>Only %d is supported right now.                                                                                                                                                                           |
| Date     | **{**_format_character_**}** | same as [date](https://www.php.net/manual/en/function.date.php#refsect1-function.date-parameters)<br>EACH Date format should wrap separately.<br>Currently supports: `YyMmWdwNHi`<br>if you want 19970101, you should do {Y}{m}{d} |
| Property | **\[**_property_name_**]**   | Powered by [Symfony's PropertyAccess Component](https://symfony.com/doc/current/components/property_access.html)                                                                                                                   |
