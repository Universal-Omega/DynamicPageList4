<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\DynamicPageList4\Maintenance;

use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use Wikimedia\Rdbms\DBQueryError;
use Wikimedia\Rdbms\IMaintainableDatabase;
use const DB_PRIMARY;

class DropView extends LoggedUpdateMaintenance {

	public function __construct() {
		parent::__construct();

		$this->addDescription( 'Drop the DPL4 VIEW if it exists.' );
		$this->requireExtension( 'DynamicPageList4' );
	}

	protected function getUpdateKey(): string {
		return 'dynamic-page-list-4-drop-view';
	}

	public function updateSkippedMessage(): string {
		return 'VIEW does not exist.';
	}

	protected function doDBUpdates(): bool {
		$dbw = $this->getDB( DB_PRIMARY );
		if ( $dbw->tableExists( 'dpl_clview', __METHOD__ ) ) {
			try {
				$viewName = $dbw->tableName( 'dpl_clview' );
				$dbw->query( "DROP VIEW IF EXISTS $viewName;", __METHOD__ );
				$this->output( "Dropped VIEW $viewName.\n" );
			} catch ( DBQueryError $e ) {
				$this->output( "Failed to drop VIEW: {$e->getMessage()}\n" );
			}

			return true;
		}

		$this->output( "VIEW does not exist.\n" );
		return true;
	}
}

// @codeCoverageIgnoreStart
return DropView::class;
// @codeCoverageIgnoreEnd
