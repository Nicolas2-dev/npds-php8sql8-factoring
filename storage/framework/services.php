<?php

return array (
  'providers' => 
  array (
    0 => 'Two\\Auth\\AuthServiceProvider',
    1 => 'Two\\Bus\\BusServiceProvider',
    2 => 'Two\\Broadcasting\\BroadcastServiceProvider',
    3 => 'Two\\Cache\\CacheServiceProvider',
    4 => 'Two\\Routing\\RoutingServiceProvider',
    5 => 'Two\\Cookie\\CookieServiceProvider',
    6 => 'Two\\Database\\DatabaseServiceProvider',
    7 => 'Two\\Encryption\\EncryptionServiceProvider',
    8 => 'Two\\Filesystem\\FilesystemServiceProvider',
    9 => 'Two\\Localization\\LocalizationServiceProvider',
    10 => 'Two\\Hashing\\HashServiceProvider',
    11 => 'Two\\Mail\\MailServiceProvider',
    12 => 'Two\\Notifications\\NotificationServiceProvider',
    13 => 'Two\\Packages\\PackageServiceProvider',
    14 => 'Two\\Pagination\\PaginationServiceProvider',
    15 => 'Two\\Queue\\QueueServiceProvider',
    16 => 'Two\\Redis\\RedisServiceProvider',
    17 => 'Two\\Session\\SessionServiceProvider',
    18 => 'Two\\Validation\\ValidationServiceProvider',
    19 => 'Two\\View\\ViewServiceProvider',
    20 => 'Two\\Cache\\ConsoleServiceProvider',
    21 => 'Two\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    22 => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    23 => 'Two\\Database\\MigrationServiceProvider',
    24 => 'Two\\Database\\SeedingServiceProvider',
    25 => 'Two\\Localization\\ConsoleServiceProvider',
    26 => 'Two\\Notifications\\ConsoleServiceProvider',
    27 => 'Two\\Packages\\ConsoleServiceProvider',
    28 => 'Two\\Routing\\ConsoleServiceProvider',
    29 => 'Two\\Session\\ConsoleServiceProvider',
    30 => 'App\\Providers\\AppServiceProvider',
    31 => 'App\\Providers\\AuthServiceProvider',
    32 => 'App\\Providers\\EventServiceProvider',
    33 => 'App\\Providers\\RouteServiceProvider',
    34 => 'App\\Providers\\BroadcastServiceProvider',
    35 => 'Shared\\RgpdCitron\\RgpdCitronServiceProvider',
    36 => 'Shared\\TinyMce\\TinyMceServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Two\\Auth\\AuthServiceProvider',
    1 => 'Two\\Broadcasting\\BroadcastServiceProvider',
    2 => 'Two\\Routing\\RoutingServiceProvider',
    3 => 'Two\\Cookie\\CookieServiceProvider',
    4 => 'Two\\Database\\DatabaseServiceProvider',
    5 => 'Two\\Encryption\\EncryptionServiceProvider',
    6 => 'Two\\Filesystem\\FilesystemServiceProvider',
    7 => 'Two\\Packages\\PackageServiceProvider',
    8 => 'Two\\Pagination\\PaginationServiceProvider',
    9 => 'Two\\Session\\SessionServiceProvider',
    10 => 'Two\\Packages\\ConsoleServiceProvider',
    11 => 'App\\Providers\\AppServiceProvider',
    12 => 'App\\Providers\\AuthServiceProvider',
    13 => 'App\\Providers\\EventServiceProvider',
    14 => 'App\\Providers\\RouteServiceProvider',
    15 => 'App\\Providers\\BroadcastServiceProvider',
  ),
  'deferred' => 
  array (
    'Two\\Bus\\Dispatcher' => 'Two\\Bus\\BusServiceProvider',
    'Two\\Bus\\DispatcherInterface' => 'Two\\Bus\\BusServiceProvider',
    'Two\\Bus\\QueueingDispatcherInterface' => 'Two\\Bus\\BusServiceProvider',
    'cache' => 'Two\\Cache\\CacheServiceProvider',
    'cache.store' => 'Two\\Cache\\CacheServiceProvider',
    'memcached.connector' => 'Two\\Cache\\CacheServiceProvider',
    'language' => 'Two\\Localization\\LocalizationServiceProvider',
    'hash' => 'Two\\Hashing\\HashServiceProvider',
    'mailer' => 'Two\\Mail\\MailServiceProvider',
    'swift.mailer' => 'Two\\Mail\\MailServiceProvider',
    'swift.transport' => 'Two\\Mail\\MailServiceProvider',
    'notifications' => 'Two\\Notifications\\NotificationServiceProvider',
    'queue' => 'Two\\Queue\\QueueServiceProvider',
    'queue.worker' => 'Two\\Queue\\QueueServiceProvider',
    'queue.listener' => 'Two\\Queue\\QueueServiceProvider',
    'queue.failer' => 'Two\\Queue\\QueueServiceProvider',
    'command.queue.work' => 'Two\\Queue\\QueueServiceProvider',
    'command.queue.listen' => 'Two\\Queue\\QueueServiceProvider',
    'command.queue.restart' => 'Two\\Queue\\QueueServiceProvider',
    'command.queue.subscribe' => 'Two\\Queue\\QueueServiceProvider',
    'queue.connection' => 'Two\\Queue\\QueueServiceProvider',
    'redis' => 'Two\\Redis\\RedisServiceProvider',
    'validator' => 'Two\\Validation\\ValidationServiceProvider',
    'view' => 'Two\\View\\ViewServiceProvider',
    'view.finder' => 'Two\\View\\ViewServiceProvider',
    'view.engine.resolver' => 'Two\\View\\ViewServiceProvider',
    'template' => 'Two\\View\\ViewServiceProvider',
    'template.compiler' => 'Two\\View\\ViewServiceProvider',
    'markdown' => 'Two\\View\\ViewServiceProvider',
    'markdown.compiler' => 'Two\\View\\ViewServiceProvider',
    'section' => 'Two\\View\\ViewServiceProvider',
    'command.cache.clear' => 'Two\\Cache\\ConsoleServiceProvider',
    'command.cache.forget' => 'Two\\Cache\\ConsoleServiceProvider',
    'command.cache.table' => 'Two\\Cache\\ConsoleServiceProvider',
    'composer' => 'Two\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'forge' => 'Two\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.asset.publish' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.config.publish' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.clear-compiled' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.clear-log' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.console.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.down' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.environment' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.event.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.job.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.key.generate' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.listener.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.model.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.optimize' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.policy.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.provider.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.request.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.serve' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.shared.make' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.assets-link' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.up' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.vendor.publish' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.view.clear' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'command.view.publish' => 'Two\\Foundation\\Providers\\ForgeServiceProvider',
    'migrator' => 'Two\\Database\\MigrationServiceProvider',
    'migration.repository' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate.rollback' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate.reset' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate.refresh' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate.install' => 'Two\\Database\\MigrationServiceProvider',
    'migration.creator' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate.make' => 'Two\\Database\\MigrationServiceProvider',
    'command.migrate.status' => 'Two\\Database\\MigrationServiceProvider',
    'seeder' => 'Two\\Database\\SeedingServiceProvider',
    'command.seed' => 'Two\\Database\\SeedingServiceProvider',
    'command.seeder.make' => 'Two\\Database\\SeedingServiceProvider',
    'command.languages.update' => 'Two\\Localization\\ConsoleServiceProvider',
    'command.notification.table' => 'Two\\Notifications\\ConsoleServiceProvider',
    'command.notification.make' => 'Two\\Notifications\\ConsoleServiceProvider',
    'command.controller.make' => 'Two\\Routing\\ConsoleServiceProvider',
    'command.middleware.make' => 'Two\\Routing\\ConsoleServiceProvider',
    'command.route.list' => 'Two\\Routing\\ConsoleServiceProvider',
    'command.session.database' => 'Two\\Session\\ConsoleServiceProvider',
    'two-rgpd' => 'Shared\\RgpdCitron\\RgpdCitronServiceProvider',
    'two-editeur-tinymce' => 'Shared\\TinyMce\\TinyMceServiceProvider',
  ),
  'when' => 
  array (
    'Two\\Bus\\BusServiceProvider' => 
    array (
    ),
    'Two\\Cache\\CacheServiceProvider' => 
    array (
    ),
    'Two\\Localization\\LocalizationServiceProvider' => 
    array (
    ),
    'Two\\Hashing\\HashServiceProvider' => 
    array (
    ),
    'Two\\Mail\\MailServiceProvider' => 
    array (
    ),
    'Two\\Notifications\\NotificationServiceProvider' => 
    array (
    ),
    'Two\\Queue\\QueueServiceProvider' => 
    array (
    ),
    'Two\\Redis\\RedisServiceProvider' => 
    array (
    ),
    'Two\\Validation\\ValidationServiceProvider' => 
    array (
    ),
    'Two\\View\\ViewServiceProvider' => 
    array (
    ),
    'Two\\Cache\\ConsoleServiceProvider' => 
    array (
    ),
    'Two\\Foundation\\Providers\\ConsoleSupportServiceProvider' => 
    array (
    ),
    'Two\\Foundation\\Providers\\ForgeServiceProvider' => 
    array (
    ),
    'Two\\Database\\MigrationServiceProvider' => 
    array (
    ),
    'Two\\Database\\SeedingServiceProvider' => 
    array (
    ),
    'Two\\Localization\\ConsoleServiceProvider' => 
    array (
    ),
    'Two\\Notifications\\ConsoleServiceProvider' => 
    array (
    ),
    'Two\\Routing\\ConsoleServiceProvider' => 
    array (
    ),
    'Two\\Session\\ConsoleServiceProvider' => 
    array (
    ),
    'Shared\\RgpdCitron\\RgpdCitronServiceProvider' => 
    array (
    ),
    'Shared\\TinyMce\\TinyMceServiceProvider' => 
    array (
    ),
  ),
);
