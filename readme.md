# Tracy plugin pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    tracyPlugin: NAttreid\TracyPlugin\DI\TracyExtension
```

dostupné nastavení
```neon
tracyPlugin:
    cookie: tz6h8dh6dt7
    mailPanel: true
    mailPath: %tempDir%/mail-panel-mails
```

## Nastavení v administraci
```php
/** @var \NAttreid\TracyPlugin\Tracy @inject */
public $tracy;

$this->tracy->enable();
// nebo
$this->tracy->disable();
```