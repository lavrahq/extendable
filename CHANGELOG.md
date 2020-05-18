# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

You can find and compare releases at the [GitHub release page](https://github.com/lavrahq/extendable/releases).

## Unreleased

-   Resolves additional issues with trying to connect to database before database is configured. [#3](https://github.com/lavrahq/extendable/pull/3)

## [0.1.1](https://github.com/lavrahq/extendable/releases/tag/v0.1.1)

### Fixed

-   Resolves issue with trying to connect to database before database is configured. [#2](https://github.com/lavrahq/extendable/pull/2)

## [0.1.0](https://github.com/lavrahq/extendable/releases/tag/v0.1.0)

The first official release of the `extendable` package, providing full support for extending
a Laravel application using `extender` classes. This release also starts support for themes and
provides a `ViewFinder` to locate views in the registered themes.

At the current time, support for registering a Theme and theme's parent is supported, but there is
not support for asset directories. Asset directories are coming in the next release.

### Added

-   Added ability to extend Middleware, Service Providers, and Translations
-   Support for adding metadata in `composer.json` within the `extra.extension` key
-   Support for adding a `provides` array in `extra.extension` with a string of `theme` to enable
    the extension to broadcast that is can theme the interface.
-   Support for having an extension enabled while not using it as a theme.
-   Support for providing migrations for themes in the `migrations` directory or setting a directory within the
    manifest as a `extra.extension.migrations` key.
