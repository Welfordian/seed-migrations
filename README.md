# Seed Migrations

Seeds should be ran from the `App\Database\Seeds` namespace as, by default, Laravel seeds aren't PSR compliant.

Once the package has been installed run the migration using `a migrate`.

An example seed using the `SeedsMigrations` class.

```
<?php

namespace App\Database\Seeds;

use \Welfordian\SeedMigrations\SeedsMigrations;

class CreatePermissions extends SeedsMigrations
{
    public function handle()
    {
        $lang = new \App\Language();

        $lang->name = "English";

        $lang->save();
    }
}
```

After a seed has been created you can seed using `a db:seed` and undo the seed using `a db:unseed`
