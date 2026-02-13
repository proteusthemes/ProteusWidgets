# ProteusWidgets #
This is a composer package of the **ProteusThemes** widgets that are used in latest themes (CargoPress and up). 

### Deps which needs to be included in the theme

- [Font Awesome](http://fortawesome.github.io/Font-Awesome/) CSS file with handle `font-awesome` on admin.
- [Mustache.js](https://github.com/janl/mustache.js) with handle `mustache.js` on admin.

---

## Branch consolidation (2026-02-12)

The `v3` branch has been folded into `master`. Previously, `master` held the v2.x line (Mustache-based, frozen since April 2018) while `v3` (Plates-based) was the active default branch. Since no theme uses v2.x anymore, the branches have been consolidated:

- Old `master` (v2.x) archived as the `v2` branch
- `master` now contains what was previously `v3`
- `v3` branch deleted
- `v4` branch remains unchanged (used by woondershop-pt)

All theme Composer dependencies use version tags (e.g. `^3.16.13`), not branch names, so this change has no effect on dependency resolution.
