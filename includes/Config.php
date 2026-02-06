<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\DynamicPageList4;

use MediaWiki\Config\HashConfig;
use MediaWiki\Config\MultiConfig;
use MediaWiki\Debug\MWDebug;
use MediaWiki\MediaWikiServices;
use function is_array;

class Config extends MultiConfig {

	private static ?self $instance = null;

	private function __construct() {
		$configFactory = MediaWikiServices::getInstance()->getConfigFactory();
		$config = $configFactory->makeConfig( 'DynamicPageList4' );

		$legacy = [];
		if ( $config->has( 'DplSettings' ) && is_array( $config->get( 'DplSettings' ) ) ) {
			$legacy = $config->get( 'DplSettings' );
		}

		if ( $legacy === [] ) {
			parent::__construct( [ $config ] );
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
			$config,
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
