<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\DynamicPageList4;

use MediaWiki\Config\GlobalVarConfig;
use MediaWiki\Config\HashConfig;
use MediaWiki\Config\MultiConfig;
use MediaWiki\Debug\MWDebug;
use function is_array;

class Config extends MultiConfig {

	private static ?self $instance = null;

	private function __construct() {
		$globalConfig = new GlobalVarConfig();

		$legacy = [];
		if ( $globalConfig->has( 'DplSettings' ) && is_array( $globalConfig->get( 'DplSettings' ) ) ) {
			$legacy = $globalConfig->get( 'DplSettings' );
		}

		if ( $legacy === [] ) {
			parent::__construct( [ $globalConfig ] );
			return;
		}

		$overrides = self::buildOverridesFromLegacy( $legacy );
		if ( $overrides !== [] ) {
			MWDebug::deprecatedMsg(
				msg: '$wgDplSettings is deprecated (use $wgDPL* variables instead)',
				component: 'DynamicPageList4'
			);
		}

		parent::__construct( [
			new HashConfig( $overrides ),
			$globalConfig,
		] );
	}

	private static function buildOverridesFromLegacy( array $legacySettings ): array {
		$map = [
			'allowedNamespaces' => ConfigNames::AllowedNamespaces,
			'allowUnlimitedCategories' => ConfigNames::AllowUnlimitedCategories,
			'allowUnlimitedResults' => ConfigNames::AllowUnlimitedResults,
			'alwaysCacheResults' => ConfigNames::AlwaysCacheResults,
			'categoryStyleListCutoff' => ConfigNames::CategoryStyleListCutoff,
			'functionalRichness' => ConfigNames::FunctionalRichness,
			'maxCategoryCount' => ConfigNames::MaxCategoryCount,
			'maxQueryTime' => ConfigNames::MaxQueryTime,
			'maxResultCount' => ConfigNames::MaxResultCount,
			'minCategoryCount' => ConfigNames::MinCategoryCount,
			'overrideParameterDefaults' => ConfigNames::OverrideParameterDefaults,
			'queryCacheTime' => ConfigNames::QueryCacheTime,
			'recursivePreprocess' => ConfigNames::RecursivePreprocess,
			'recursiveTagParse' => ConfigNames::RecursiveTagParse,
			'runFromProtectedPagesOnly' => ConfigNames::RunFromProtectedPagesOnly,
		];

		$overrides = [];
		foreach ( $map as $legacyKey => $newName ) {
			if ( isset( $legacySettings[$legacyKey] ) ) {
				$overrides[$newName] = $legacySettings[$legacyKey];
			}
		}

		return $overrides;
	}

	public static function getInstance(): self {
		self::$instance ??= new self();
		return self::$instance;
	}
}
