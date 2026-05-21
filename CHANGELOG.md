# Changelog

All notable changes to ProteusWidgets are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [5.0.0] – TBD

> Major release. Downstream theme maintainers consuming ProteusWidgets via Composer should review the **Breaking changes** section below before upgrading from 4.x.

### Breaking changes

- Removed public method `PW_Functions::get_full_fa_class()`. The Font Awesome class-name helper that expanded shorthand like `fa-facebook` to a fully prefixed FA5 class string is gone with no shim. Themes that called this method directly will fatal on upgrade; replace call sites with the bare Font Awesome class string emitted in the widget option (e.g. `class="fa <icon>"`).
- Font Awesome class convention changed in view templates. Widget views now hard-code `class="fa <icon>"` (FA4-style single prefix) instead of running the icon string through `get_full_fa_class()`. Themes that override these view files, or that ship CSS targeting `.fab.fa-*` / `.fas.fa-*` / `.far.fa-*` selectors, may need adjustments.
- Filter `pw/default_social_icon` default value changed from `fab fa-facebook` to `fa-facebook`. The filter name and signature are unchanged.
- Bootstrap 3 `col-xs-*` classes removed from widget grid markup. Themes still using the Bootstrap 3 grid may see layout differences on extra-small viewports.

### Added

- MentalPress widget set (#14).
- ConsultPress widget updates (#15).
- Bolts widget set (#12).
- Contact-profile widget (#13).
- Pricing-package fields-in-item (#16).
- MedicPress-specific widgets (#9).
- Edit-page link in the Featured Page widget (#5).
- WPML config coverage for Person Profile (#11), Adrenaline widgets (#8), Autocomplete (#7), Featured Product (#10), and Featured Page widget (`bf16a06`).
- `bin/check-dep-age.php` plus `.github/workflows/dep-age.yml`: CI gate that rejects locked dependencies less than 7 days old (closes #19).
- `.github/workflows/tests.yml`: PHPUnit runs on every PR and push to `master`, against PHP 8.5 with a MySQL 9 service.

### Changed

- Bootstrap grid markup migrated from Bootstrap 3 to Bootstrap 4 conventions (`5963892`).
- i18n pipeline migrated from Grunt to WP-CLI scripts in `package.json` (`npm run i18n:pot`, `i18n:po`, `i18n:mo`, `i18n`) (`e56019e`).
- PHPUnit test suite modernized to integrate with the official WordPress test library via `bin/install-wp-tests.sh` and `tests/bootstrap.php` (`4ca0ae9`).
- CI now runs on PHP 8.5 with MySQL 9 and `actions/checkout@v6` (`fde0b76`).
- `LICENSE` replaced with `LICENSE.md` to match the GPL 2.0 only text actually offered (`50a57de`).
- `composer.json` `license` SPDX id updated from the deprecated `GPL-2.0+` to `GPL-2.0-or-later`.
- `league/plates` Composer constraint relaxed from `^3.4.0` to `^3.1.1`.
- `composer.lock` regenerated; `require-dev` now pins `phpunit/phpunit ^9.6` and `yoast/phpunit-polyfills ^2.0`.

### Fixed

- Sparse widget instances no longer warn under PHP 8 — `wp_parse_args()` defaults applied before widget rendering (#18, `394f2b9`).
- Featured Page widget: PHP 8 null-safety and WPML translation support (`bf16a06`).
- Removing a repeater field now triggers the widget save button (`866e722`).
- PHP 8 deprecation warning from a private magic method in `PW_Functions` (`a714e90`).
- Missing space in the Latest News widget output (`c46ed15`).
- Missing curly brace in widget markup (`8c22f4b`).
- Broken Font Awesome stylesheet link (`5432310`).

### Removed

- Public method `PW_Functions::get_full_fa_class()` (see **Breaking changes** above).
- Travis CI configuration (`aa4b637`) — replaced by GitHub Actions.
- `Gruntfile.js`-driven i18n — superseded by WP-CLI scripts.
- `col-xs-*` class names from view templates.

[Unreleased]: https://github.com/proteusthemes/ProteusWidgets/compare/v5.0.0...HEAD
[5.0.0]: https://github.com/proteusthemes/ProteusWidgets/compare/v4.0.6...v5.0.0
