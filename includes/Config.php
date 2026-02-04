<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\DynamicPageList4;

use MediaWiki\Config\GlobalVarConfig;
use MediaWiki\Config\HashConfig;
use MediaWiki\Config\MultiConfig;
use MediaWiki\Debug\MWDebug;
use function array_key_exists;
use function is_array;

class Config extends MultiConfig {

	private static ?self $instance = null;

	private function __construct() {
		$globalConfig = new GlobalVarConfig();

		// Legacy, historically: $wgDplSettings, deprecated in favor of $wgDPL*.
		$legacySettings = [];
		if ( $globalConfig->has( 'DplSettings' ) && is_array( $globalConfig->get( 'DplSettings' ) ) ) {
			$legacySettings = $globalConfig->get( 'DplSettings' );
		}

		[ $normalizedSettings, $usedLegacy ] = self::buildSettingsFromGlobalsAndLegacy( $globalConfig, $legacySettings );

		if ( $usedLegacy ) {
			MWDebug::deprecatedMsg(
				msg: '$wgDplSettings is deprecated (use $wgDPL* globals instead)',
				component: 'DynamicPageList4'
			);
		}

		parent::__construct( [
			new HashConfig( $normalizedSettings ),
			$globalConfig,
		] );
	}

	/**
	 * Prefer $wgDPL* globals, fallback to legacy $wgDplSettings array.
	 *
	 * @return array{0: array, 1: bool} [ settingsArray, usedLegacy ]
	 */
	private static function buildSettingsFromGlobalsAndLegacy(
		GlobalVarConfig $globalConfig,
		array $legacySettings
	): array {
		$usedLegacy = false;

		$map = [
			'allowedNamespaces' => 'DPLAllowedNamespaces',
			'allowUnlimitedCategories' => 'DPLAllowUnlimitedCategories',
			'allowUnlimitedResults' => 'DPLAllowUnlimitedResults',
			'alwaysCacheResults' => 'DPLAlwaysCacheResults',
			'categoryStyleListCutoff' => 'DPLCategoryStyleListCutoff',
			'functionalRichness' => 'DPLFunctionalRichness',
			'maxCategoryCount' => 'DPLMaxCategoryCount',
			'maxQueryTime' => 'DPLMaxQueryTime',
			'maxResultCount' => 'DPLMaxResultCount',
			'minCategoryCount' => 'DPLMinCategoryCount',
			'overrideParameterDefaults' => 'DPLOverrideParameterDefaults',
			'queryCacheTime' => 'DPLQueryCacheTime',
			'recursivePreprocess' => 'DPLRecursivePreprocess',
			'recursiveTagParse' => 'DPLRecursiveTagParse',
			'runFromProtectedPagesOnly' => 'DPLRunFromProtectedPagesOnly',
		];

		$settings = [];
		foreach ( $map as $key => $newGlobalName ) {
			if ( $globalConfig->has( $newGlobalName ) ) {
				$settings[$key] = $globalConfig->get( $newGlobalName );
				continue;
			}

			if ( array_key_exists( $key, $legacySettings ) ) {
				$settings[$key] = $legacySettings[$key];
				$usedLegacy = true;
			}
		}

		return [ $settings, $usedLegacy ];
	}

	public static function getInstance(): self {
		self::$instance ??= new self();
		return self::$instance;
	}
}
