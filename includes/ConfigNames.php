<?php

declare( strict_types = 1 );

// phpcs:disable Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
namespace MediaWiki\Extension\DynamicPageList4;

/**
 * A class containing constants representing the names of configuration variables,
 * to protect against typos.
 */
final class ConfigNames {

	public const AllowedNamespaces = 'DPLAllowedNamespaces';

	public const AllowUnlimitedCategories = 'DPLAllowUnlimitedCategories';

	public const AllowUnlimitedResults = 'DPLAllowUnlimitedResults';

	public const AlwaysCacheResults = 'DPLAlwaysCacheResults';

	public const CategoryStyleListCutoff = 'DPLCategoryStyleListCutoff';

	public const FunctionalRichness = 'DPLFunctionalRichness';

	public const MaxCategoryCount = 'DPLMaxCategoryCount';

	public const MaxQueryTime = 'DPLMaxQueryTime';

	public const MaxResultCount = 'DPLMaxResultCount';

	public const MinCategoryCount = 'DPLMinCategoryCount';

	public const OverrideParameterDefaults = 'DPLOverrideParameterDefaults';

	public const QueryCacheTime = 'DPLQueryCacheTime';

	public const RecursivePreprocess = 'DPLRecursivePreprocess';

	public const RecursiveTagParse = 'DPLRecursiveTagParse';

	public const RunFromProtectedPagesOnly = 'DPLRunFromProtectedPagesOnly';
}
