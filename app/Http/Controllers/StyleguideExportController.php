<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class StyleguideExportController extends Controller {

	protected $export;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct( \App\Services\ExportService $export ) {
		$this->export = $export;
	}

	public function export( $id ) {
		$data = Project::find( $id );

		// Project zip.
		$zip_file = $this->export->get( $data );

		// Download and delete project zip.
		$response = response()->download( $zip_file )->deleteFileAfterSend(true);

		ob_end_clean();

		return $response;

//		return response()->json( Project::find( $id ) );
	}

}
