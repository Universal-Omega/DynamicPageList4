# Version 4.0.2
* Removes the use of `dpl_clview`. Running `update.php` will automatically drop the view.
* Fixes issues with the use of `_none_` to get uncategorized pages.

# Version 4.0.1
* Drops support for MediaWiki 1.43.
* Fixes a bug with the usage of `distinct` in queries.
* Supports `categorylinks` migration for MediaWiki 1.45.
  * **NOTE**: When upgrading to MediaWiki 1.45 you will also need to recreate the view by running the `CreateView` maintenance script with the `--recreate` option, to apply changes to it for the `categorylinks` migration.

# Version 4.0.0
**The extension was nearly completely rewritten, as such it has been rebranded as DynamicPageList4 as well. There could also be other changes not mentioned here as well, but major changes should be mentioned here.**
* Converted all queries to use MediaWiki's QueryBuilder
  * Has better performance and security
  * More maintainable
  * Fixes many bugs with queries
* `openreferences` is fixed so it actually gives the openreferences conflict error rather than a query error for `openreferences = missing`.
* `ordermethod = pagesel` gives a new error message rather than a query error when not using any of the supported options with it.
* Fixed some instances of `column reference "page_title" is ambiguous`.
* Added support for `linksfrom`, `linksto`, `usedby`, and `uses` to be used together at the same time. Previously mixing them would either give errors or return no results.
* Added more support for PostgresSQL and SQLite for many more parameters (such as `ignorecase`) that previously only worked on MySQL and MariaDB.
* Fixed a bug where order method was set to the wrong type when using intersection-like tags which caused fatals.
* Fixed compatability with FlaggedRevs — `qualitypages` and `stablepages` now function again. They have not worked for a very long time. This was fixed by replacing the check for `efLoadFlaggedRevs` functions with checking by using `ExtensionRegistry`.
* Fixed `ordercollation` (very long time bug). Additionally, the `ordercollation` parameter now accepts just charsets rather than full collation as well (only when using MySQL or MariaDB though). `ordercollation` is now also compatible with SQLite for a small subset of default collations available in SQLite (`BINARY`, `NOCASE`, and `RTRIM`). Most collations should work properly for MySQL/MariaDB and PostgreSQL as well now.
* Added support for using regular expressions (`REGEXP`) on PostgreSQL.
* `openreferences` now correctly produces the expected conflict error instead of a query error when misused.
* Fixed handling of `catheadings` and `catnotheadings` (via `+` and `-` in `category=`). Previously, `category=+` would error and `category=-` could yield unpredictable results.
* Updated symbols within `%ARGS%` to avoid parser conflicts. This resolves several bugs with template expansion.
* Configuration fetching is now more reliable and uses MediaWiki's built-in mechanisms.
* Fix for counts when using `includesubpages = false`: subpage exclusion has been moved from the `Parse` class (which runs after the query) to be handled directly within the query itself.
* Fixes for bizarre corner cases where PHP's DateTime parser interpreted invalid zero-based timestamps in... interesting ways.
* Support for PostgreSQL in all the timestamp queries.
* `%EDITSUMMARY%` now correctly retrieves revision comments again.  
  * This feature had been broken since MediaWiki 1.35 due to changes in how `rev_comment_id` and `comment_text` are stored. The fix restores full functionality of this.
* Spaces in namespace names are now automatically replaced with underscores to ensure compatibility and avoid malformed queries.
* Fixed `addexternallink`, `linkstoexternal`, and `%EXTERNALLINK%`.
  * **NOTE:** This restores `linkstoexternal` behavior to how it used to be and removes `linkstoexternaldomain` and `linkstoexternalpath`.
* Improved table alias usage.
* Refactored parameter handling to replace `__call()` magic with `processParameter()`.
* Fixed the message used in `articleCountMessage`.
* Fixed issue with `colspan`.
* Added compatability with the Video extension.
* [Performance] Removed tables from `FOUND_ROWS`.
* [Performance] Optimized query for `ordermethod=lastedit`.
* [Performance] Only parse image URLs for `%IMAGE%` if needed.
* Use `strict_types`
* Don't let `titleText` become null if match fails. This fixes exceptions if regex is invalid in `replaceintitle`.
* Escape category links in gallery. Fixes creating a gallery with category pages adding every category in the gallery to the page.
* Fixed multiple bugs in `reset` and `eliminate` parameters.
* Fixed catching `DBQueryError`, and improved query exception handling, adding a new exception type, `QueryException` to more reliably and explicitly catch proper query errors.
* Fixed `nottitlematch` bug where only the first pattern is excluded.
* Added validation for regex in `replaceintitle` to avoid potential PHP warnings for invalid regex.
* Added more PHPUnit and Parser tests.

## New Features
* Added new configuration option to support overriding the parameter defaults, `$wgDplSettings['overrideParameterDefaults']`.
* Added support for string replacements in `replaceintitle`. This previously only supported regex replacements, now it will fallback to string replacements if there is no valid regex.
* Added support for display titles:
  * New format option: `%DISPLAYTITLE%` (falls back to `%TITLE%` if no display title is set; available only when using the `title` parameter).
  * New order method: `ordermethod=displaytitle` (automatically falls back to sorting by `title` when no display title exists).
* `namespace` and `notnamespace` now accepts more options (only if they do not conflict with other existing namespace names):
  * Case-insensitive `main` is now accepted for the main namespace.
  * Namespace IDs (rather than just namespace names) work for all namespaces.
* Support for a wide range of relative timestamps (e.g., "last week", "in 2 hours", "1 month ago").
  Previously only a few relative timestamps where supported, now anything supported by PHPs `strtotime` is supported.
* **Gallery Mode Enhancements:**  
  * Properly supports retrieving **PageImages** when using `mode=gallery` on non-file namespace pages, if the **PageImages** extension is installed. 
  * Now pulls directly from the **PageImages** extension instead of relying on `page_props`, allowing access to both `page_image` and `page_image_free`.
  * Added support for `mode=gallery` when using **Intersection compatibility mode** (i.e., `<DynamicPageList>` tags).
  * Improves support for `%IMAGE%`:  
    It no longer relies on stock `/images/` directories and instead dynamically resolves images based on whatever file backend is in use.
  * Introduces two new parameters: `imagewidth` and `imageheight`, to control the size of images within galleries.
  * Introduces another new parameter: `gallerymode`.  
    This sets the `mode=` attribute in the generated `<gallery>` tag. Defaults to **`traditional`**, but supports all standard modes:
    * `traditional`
    * `nolines`
    * `packed`
    * `packed-hover`
    * `packed-overlay`
    * `slideshow`
 
  For more on supported gallery modes, see:  
https://www.mediawiki.org/wiki/Help:Images#Gallery_syntax

## BREAKING CHANGES
Previously, using `&` in a category name would incorrectly be interpreted as a logical AND separator between multiple categories, instead of being treated as a literal character in the category name. This meant that querying for categories containing an actual `&` (like *"Cats & Dogs"*) would fail to match any pages.

  This behavior has been fixed:
  * To perform a logical `AND` between categories, you now must explicitly use `<&>`.
  * A literal `&` in category names will now be treated correctly as part of the name.

----

Previously, underscores (`_`) in LIKE-style patterns (like in `titlematch` and `nottitlematch`) were implicitly treated as single-character wildcards. This made it impossible to reliably match literal underscores (which also represent spaces), and caused patterns to match more broadly than intended.

This behavior has been fixed:

* A single-character wildcard must now be written explicitly as `[_]`.
* Each `[_]` represents exactly one character (for example, `[_][_][_]` matches three characters).
* A bare `_` is now treated as a literal underscore and will no longer act as a wildcard.

**NOTE:** We do it with brackets, because brackets are disallowed title characters, so they won't cause issues in the future with some edge-cases since no title can actually contain them. For similar reasons in the future, the same or similar method may be needed for `%` in order to allow better matching of literal `%` in titles, but that is a change that will break much more uses so will be done differently, perhaps keeping existing behavior but adding some other substitution to match literal `%`. For now, matching literal `%` remains unsupported or unreliable.

----

The template transclusion (`Extension DPL`) has been replaced with a proper tracking category, `Pages using DynamicPageList4`. All the references and usages of `Extension DPL` has been removed, including the `CreateTemplate` maintenance script. There is now a new maintenance script, `DeleteTemplate` to delete the old template. It will auto run when updated using `update.php`.

----

`notcategory` now applies to the `maxCategoryCount` limitation as well. This was done because they both use queries, so the limit, designed for query limitation, should apply to both. This may break existing pages as well, so the limit may need changed in user's configuration as well.

----

Removed the `suppresserrors` parameter.
<br />**NOTE:** This one was documented as deprecated and slated for removal long ago and hasn't done anything at all prior to removal.

----

Removed support for updating/deleting articles.
<br />**NOTE:** This removed the `deleterules` and `updaterules` parameters, and all associated user rights and related code including the `UpdateArticle` class entirely.

----

Updated some config defaults, changing:
* `$wgDplSettings['allowedNamespaces']` to be an empty array (`[]`) instead of `null`. 
* `$wgDplSettings['recursivePreprocess']` to be enabled by default.
* `$wgDplSettings['maxCategoryCount']` increased to 8 instead of 4.
  * This was done since `notcategory` now applies to the limit as well, so since double the parameters affect it, the default limit was doubled as well.

----

Removed the `handleSectionTag` config. It served no purpose anymore. It was just making `<section>` behave like `<dpl>` which doesnt even seem was ever the intent for it.
