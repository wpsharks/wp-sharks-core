## v160709.27823

- Upgrading to latest websharks/core.

## v160709.24636

- Upgrading to latest websharks/core w/ Simple Expression utils.

## v160706.64464

- Bug fix. `short_var` and `short_data_var` removed in favor of Transient Shortlinks.

## v160705.82876

- Adding `s::restActionUrl()`.
- Adding `s::restActionVar()`.
- Adding `s::restActionDataVar()`.

## v160705.60576

- Bug fix. `Shortlink` is a one-word reference.

## v160705.21987

- Adding `transientShortlink()` API and Facade.
- Enhancing ReST action handlers. New method `addApiVersion()`.

## v160705.16389

- Enhancing `RestAction{}` utils.
- Adding new `RestAction{}` behavioral arg: `use_short_vars`.
- Bug fix. `RestAction{}` URLs should only contain an Nonce if applicable.

## v160703.72161

- Bug fix. Template utility class w/ wrong namespace.

## v160703.71419

- Adding `s::localToUtc()`. Converts a local timestamp into UTC time.
- Adding `s::utcToLocal()`. Converts a UTCE timestamp into a local time.
- Bug fix in `s::i18nUtc()` with respect to timezone chars not handled properly by `date_i18n()` in WordPress.
- API call for package URL (in pro software) now uses `site` instead of `location` following an update at wpsharks.com.
- Adding `CORE_CONTAINER_SLUG` constant to base `App{}` class. Used in a few default directory locations at this time.
- Extending `Template{}` class in WSC to allow for WP themes to override any default templates.

## v160702.65139

- Enhance license key placeholder in installation notice.
- Security hardening; disallow multi-dimensional ReST action data by default.
- Adding ability to define a custom `$slug` for fatalities that occur in AJAX/API handlers.

## v160701.57880

- Updating to latest WSC.
- Nest all theme/plugin logs/cache into `.wp-sharks` sub-directory.

## v160630.69234

- Updating to the latest WSC.
- Adding additional brand-related config keys and URL generators.
- Enhancing transient utilities. Adding support for DB-driven transients via hash IDs.
- Bug fix. `$uri` in Updater class was not initialized properly.

## v160629.60185

- Updating to latest WSC.
- Updating to latest phings.
- Updating `.build.props` file.
- Updating Git dotfiles.

## v160625.60388

- Adding support for ReST action API versions.
- Adding brand URLs for API, CDN, and stats.
- Adding new brand URL utilities for API, CDN, and stats.
- Updating API calls back to wpsharks.com for licensing.
- Adding support for custom fatal error messages associated w/ AJAX and API calls.
- Adding `REST_ACTION_API_VERSION` constant to base App class.
- Forcing an API version into all API action URLs.
- Upgrading to the latest websharks/core release.
- Future-proofing WC utility methods.

## v160624.38294

- Enhancing action vars.

## v160624.33470

- Upgrading to latest websharks/core.

## v160622.85801

- Upgrading to latest websharks/core w/ enhanced output utils.

## v160621.42233

- Updating to latest core with enhanced MailChimp utils.

## v160621.35874

- Updating to latest core with MailChimp utils.
- Enhancing ReST action utilities.

## v160620.31255

- Now flushing the OPcache automatically whenever a theme, plugin, or core upgrade occurs. Referencing: <https://core.trac.wordpress.org/ticket/36455>

## v160620.27545

- Updating to websharks/core v160620.27266.

## v160608.72986

- Moving Order Item utilities from s2X to the WPSC.

## v160608.38368

- Bug fix. Should not fail on missing `$report`.
- Refactoring. `doingAction()` now `doingRestAction()` to avoid confusion w/ hooks.

## v160606.79543

- Enhancing actions, fatalities, updater, and more.
- Updating to the latest websharks/core.

## v160606.50319

- Updating to latest websharks/core.

## v160604.79078

- Updating dotfiles.
- Improving default `App->onSetupOtherHooks()` handler.
- Updating to the latest release of `websharks/core`.

## v160604.10223

- Delay license key request until after the welcome notification.
- Adding several new configurable options for notices.
- Enhance `PostsQuery` to allow for nested lists that show parent/child relationships.

## v160601.61851

- Bumping to `websharks/core` v160601.61090.
- Enhancing/refactoring action handlers and exposing them to plugins.
- Adding notice on installation to collect license key for pro software.
- Bug in notice utilities that was preventing `success` notice types from working as intended.

## v160528.39683

- Updating phing build system.
- Updating to latest websharks/core.

## v160524

- First public release.
