## [coming soon]

- Bug fix. Undefined index `active_label` on license keys menu page.
- Adding `home_url` and related properties to the centralized `Wp` base class.
- Correcting typo in dependency notice. `resolove` should be `resolve`.

## v161026.47592

- Updating to latest release of the websharks/core.
- Adding support for recurring notices, errors, and/or warnings.
- Use WordPress color pickers for `<input type="color" ...>` fields. See: <https://github.com/websharks/wp-sharks-core/issues/3>
- Adding support for free trial copies of pro software with time-limited access to all pro features.
- Bug fix. Plugins nested inside a `woocommerce` parent page were not being linked up properly in the list of plugin action links.
- Adding `s::defaultMenuPageUrl()` that collects the default menu page URL for the current plugin, based on which menu pages were added via WPSC API calls.
- Remove `Dismiss &` from trial notice buttons in favor of more concise calls to action.

## v160920.13502

- Bumping minimum WP requirement to v4.6.
- Updating to the latest WP PHP RV library.
- Enhanced remote API calls for automatic updates. Future-proofing.
- Adding `ul` and `ol` tag consideration in notice markup generation handler.

## v160908.63084

- Correcting a bug in the list of license keys that was causing a table to be displayed as `block` and therefore it lost it's table properties.
- Bug fix. Transient `hashToKey()` should return an un-prefixed key.
- Bug fix. `dieInvalid()` referenced incorrectly.

## v160831.54041

- New Facade: `s::getDefaultOption()`.
- Upgrading to latest websharks/core.
- Adding support for `meta_links` in menu pages.
- A `restore` tab is now automatically converted into a meta link.

## v160828.25227

- Automatically add `-widget` suffix in `Widget{}` base class.

## v160827.7334

- Bug fix. Only show widget when extender returns non-empty content.
- Adding `s::postMetaExists()` to check if a post has any given meta values.

## v160801.75904

- Enhancing menu page styles.
- Adding SCSS utility mixins for developers.
- Bug fix. Core URL leading to 'My Downloads' was a broken link.

## v160801.2694

- Adding `c::collectPostMeta()`.

## v160731.37352

- Adding `Classes\SCore\WidgetForm{}`
- Adding `Classes\SCore\Base\Widget{}`
- Adding `Classes\SCore\Base\Widget{}`
- Enhancing menu page styles in admin area.
- Fixed a JS bug that caused tooltips not to be shown in widget areas.

## v160726.77065

- Updating docBlocks throughout for improved codex generation coming soon.
- Adding `sami.cfg` for upcoming codex generation.
- Updating to latest release of the `websharks/core`.

## v160724.64804

- Adding `onclick` configuration key for menu page tabs.
- Adding `?in-wp` flag to brand-based URLs that open inside WordPress; e.g., changelog for a new release.

## v160724.1675

- Adding `s::postMetaKey()`.
- Adding `s::getPostMeta()`.
- Adding `s::updatePostMeta()`.
- Adding `s::deletePostMeta()`.
- Adding `s::setPostMeta()`.
- Adding `s::unsetPostMeta()`.
- Adding `s::addPostMetaBox()`.
- Adding `s::postMetaBoxForm()`.

## v160721.58752

- Updating to the latest websharks/core.
- Adding Jetpack utils for Markdown parsing.
- Bug fix in select form field generator. Use `[]` when `multiple` is true.
- Bug fix in `s::restActionFormElementName()` when passing `[]` as an array.
- Bug fix. Use `___ignore` key that forces browsers to submit an empty array.

## v160720.31351

- Refactoring menu page tab generation.
- Refactoring menu page argument names.
- Fixing a bug in automatic action links for plugins.

## v160719.39226

- Dropping `PDO` extension requirement (now optional).
- Dropping `posix` extension requirement (now optional).
- Dropping explicit PHP version requirement, because it's already specified by the websharks/core.
- Updating to the latest `websharks/core` library.

## v160718.53795

- Enhancing core menu page styles. The external link icons were being applied to buttons.

## v160716.46700

- Bug fix. Missing `$WpDb` variable in transient utils.
- Adding support for 'Check Again' functionality in `update-core.php` via `$_REQUEST['force-check']`.
- Enhancing/optimizing notice utilities.
- Enhancing/optimizing support for multisite networks and network-wide, network-only plugins.
- Enhancing menu page utilities.

## v160715.33981

- Bug fix. Core itself should ship with it's own license key.

## v160715.31125

- License key request for type: `theme`, `plugin` only.
- Updating to latest websharks/core with Simple Expression bug fixes.

## v160714.36962

- Adding `eval()` as a new PHP requirement.

## v160714.36264

- Updating to latest websharks/core.
- Adding anonymous stats collection via stats.wpsharks.io.

## v160714.26941

- Enhancing license key integration.
- Enhancing license key templates and notices.
- Updating to the latest websharks/core.
- Enhancing compatibility with a lite-to-pro transition in terms of how this impacts license keys.

## v160713.39957

- Updating to latest websharks/core.
- Adding App collection utilities needed by licensing system.
- Adding `s::addApp()`
- Adding `s::getApps()`
- Adding `s::getAppsByType()`
- Adding `s::getAppsBySlug()`
- Forcing `Classes\App::$Parent` to `null` when `$is_core`.
- Making Dicer aware of `Classes\SCore\MenuPageForm{}`.
- Refactoring to help optimize apps based on core.
- New utilities related to menu pages, styles, URLs.

## v160710.23515

- Adding `s::addMenuPage()`
- Adding `s::addMenuPageItem()`
- Adding `Classes\App::CORE_CONTAINER_VAR`
- Adding `Classes\App::CORE_CONTAINER_NAME`
- Adding `Classes\SCore\Utils::DbMenuPage{}`

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
