<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\DynamicPageList4\HookHandlers;

use MediaWiki\Extension\DynamicPageList4\Maintenance\DeleteTemplate;
use MediaWiki\Extension\DynamicPageList4\Maintenance\DropView;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class Installer implements LoadExtensionSchemaUpdatesHook {

	/**
	 * @inheritDoc
	 * @codeCoverageIgnore Tested by updating or installing MediaWiki.
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->addPostDatabaseUpdateMaintenance( DeleteTemplate::class );
		$updater->addPostDatabaseUpdateMaintenance( DropView::class );
	}
}
