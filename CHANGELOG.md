# Changelog

All notable changes to ProteusWidgets are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This file documents the v3.x → 5.0 line. The parallel 4.x line (used by WoonderShop) is unaffected by these changes and is not slated to receive a 5.0 upgrade.

## [Unreleased]

## [5.0.1] – 2026-05-22

### Fixed

- `PW_Functions::get_attachment_image_srcs()` now skips sizes where `wp_get_attachment_image_src()` returns `false` (or any non-array), preventing PHP warnings when an attachment is missing or a requested size is unavailable (#20, #21).

## [5.0.0] – 2026-05-21

> Major release. Downstream theme maintainers consuming ProteusWidgets via Composer should review the **Breaking changes** section below before upgrading from 3.16.x.

### Breaking changes

- PHP 8.0 or newer is now required. `composer.json` declares `"php": ">=8.0"`. ProteusWidgets 5.0 depends on `league/plates` releases that require PHP 8+.

### Added

- This `CHANGELOG.md`, following Keep a Changelog conventions.
- Widget views now emit both Bootstrap 3 and Bootstrap 4 grid class names (`col-N` alongside `col-xs-N`, `offset-N` alongside `col-xs-offset-N`) so themes on either grid keep rendering correctly. Affects `widgets/views/widget-author.php` and `widgets/views/widget-testimonials.php`.
- `bin/check-dep-age.php` plus `.github/workflows/dep-age.yml`: CI gate that rejects locked dependencies less than 7 days old (closes #19).
- `.github/workflows/tests.yml`: PHPUnit runs on every PR and push to `master`, against PHP 8.5 with a MySQL 9 service and both WordPress 7.0.0 and latest.
- PHPUnit suite integration with the official WordPress test library via `bin/install-wp-tests.sh` and `tests/bootstrap.php` (`4ca0ae9`).
- Sparse-widget rendering test coverage in `tests/test-sparse-widget-instances.php`.

### Changed

- i18n pipeline migrated from Grunt to WP-CLI scripts in `package.json` (`npm run i18n:pot`, `i18n:po`, `i18n:mo`, `i18n`) (`e56019e`).
- `LICENSE` replaced with `LICENSE.md` to match the GPL 2.0 only text actually offered (`50a57de`).
- `composer.json` `license` SPDX id updated from the deprecated `GPL-2.0+` to `GPL-2.0-or-later`.
- `composer.json` now declares the PHP 8.0+ runtime floor.
- `league/plates` Composer constraint relaxed from `^3.4.0` to `^3.1.1`.
- `composer.lock` regenerated; `require-dev` now pins `phpunit/phpunit ^9.6` and `yoast/phpunit-polyfills ^2.0`.

### Fixed

- Sparse widget instances no longer warn under PHP 8 — `wp_parse_args()` defaults applied before widget rendering (#18, `394f2b9`). Coverage now includes all widget render paths under WordPress 7.0 (`453aac6`).
- Opening Time no longer triggers PHP 8.5's non-canonical `(double)` cast deprecation (`453aac6`).
- `widgets/views/widget-author.php` grid markup is now usable under Bootstrap 4 themes (previously Bootstrap 3 only).
- `widgets/views/widget-testimonials.php` grid markup is now usable under Bootstrap 3 themes at the xs breakpoint (regression introduced in `5963892`, April 2024).

### Removed

- Travis CI configuration (`aa4b637`) — replaced by GitHub Actions.
- `Gruntfile.js`-driven i18n — superseded by WP-CLI scripts.

[Unreleased]: https://github.com/proteusthemes/ProteusWidgets/compare/v5.0.1...HEAD
[5.0.1]: https://github.com/proteusthemes/ProteusWidgets/compare/v5.0.0...v5.0.1
[5.0.0]: https://github.com/proteusthemes/ProteusWidgets/compare/v3.16.14...v5.0.0
