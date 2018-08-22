<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class StyleguideExportController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() { }

	public function export( $id ) {
		$data = Project::find( $id );
		dd( $data );

		return response()->json( Project::find( $id ) );
	}

}
