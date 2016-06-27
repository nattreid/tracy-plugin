# Tracy plugin pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    tracyPlugin: NAtrreid\TracyPlugin\DI\TracyExtension
```

dostupné nastavení
```neon
tracyPlugin:
    cookie: tz6h8dh6dt7
    mailPanel: @configuration::mailPanel
    mailPath: %tempDir%/mail-panel-mails
```

## Nastavení v administraci
```php
/** @var \NAtrreid\TracyPlugin\Tracy @inject */
public $tracy;

$this->tracy->enable();
// nebo
$this->tracy->disable();
```