<?php

namespace App\Services;

use App\Project;
use Storage;
use Zipper;
//use Illuminate\Support\Facades\Hash as Hash;

class ExportService {

	protected $project;

	public function __construct() {}

	public function get( Project $data ) {

		$this->project = $data;
		$project_name = strtolower( str_replace( ' ', '-', $data->name ) );
		$project_root = storage_path( 'projects' ) . '/' . $project_name;

		$colors = $this->get_colors( $data->colors_defs );
		$fonts  = $this->get_fonts( $data->typekit_fonts, $data->google_fonts, $data->web_fonts );

		// Save SCSS partial to assets.
		Storage::disk( 'projects' )->put( $project_name . '/assets/_styleguide.scss', $colors.$fonts);

		$images = $this->get_images( $data->image_defs );

		// Save images to assets.
		if ( ! empty( $images ) ) {
			foreach( $images as $image ) {
				Storage::disk( 'projects' )->put( $project_name . '/assets/' . $image['name'] . '.gif', file_get_contents( $image['src']) );
			}
		}

		echo '<pre>' . print_r( $images, TRUE ) . '</pre>';

		// Get files written to project assets directory.
		$files = glob( $project_root . '/assets/*' );

		// Zipper object.
		$zipper = new Zipper;

		// Zip all files in project assets directory.
		$zipper->make( $project_root .'/' . $project_name . '.zip')->add( $files )->close();

		return $project_root . '/' . $project_name . '.zip';

	}

	protected function get_colors( $color_data ) {

		$colors = json_decode( $color_data );

		$return_colors = "/* PROJECT COLORS */\n\n";

		if ( ! empty( $colors ) ) {
			foreach( $colors as $color ) {
				$color_var = str_replace( ' ', '-', strtolower( $color->name ) );
				$return_colors .= sprintf( "// %s\n$%s: %s;\n", $color->name, $color_var, $color->value );
			}
		}
		return $return_colors;
	}

	protected function get_fonts( $typekit_data, $google_data, $websafe_data ) {

		$fonts = '';

		$typekit_fonts = $this->get_typekit_fonts( $typekit_data );
		if ( $typekit_fonts ) {
			$fonts .= $typekit_fonts;
		}

		$google_fonts = $this->get_google_fonts( $google_data );
		if ( $google_fonts ) {
			$fonts .= $google_fonts;
		}

		$websafe_fonts = $this->get_websafe_fonts( $websafe_data );
		if ( $websafe_fonts  ) {
			$fonts .= $websafe_fonts;
		}

		return $fonts;

	}

	protected function get_typekit_fonts( $data ) {
		$fonts = json_decode( $data );

		$return_fonts = "\n\n/* FONT FAMILIES */\n\n";

		if ( ! empty( $fonts ) ) {
			foreach( $fonts as $index => $font ) {
				$count = $index + 1;
				$font_var = 'typekit-' . $count;
				$return_fonts .= sprintf( "// %s\n$%s: \"%s\";\n", $font->name, $font_var, $font->slug );
			}
		}
		return $return_fonts;
	}

	protected function get_google_fonts( $data ) {
		$fonts = json_decode( $data );

		$return_fonts = '';

		if ( ! empty( $fonts ) ) {
			foreach( $fonts as $index => $font ) {
				$count = $index + 1;
				$font_var = 'googlefont-' . $count;
				$return_fonts .= sprintf( "// %s\n$%s: \"%s\";\n", $font->name, $font_var, $font->name );
			}
		}
		return $return_fonts;
	}

	protected function get_websafe_fonts( $data ) {
		$fonts = json_decode( $data );

		$return_fonts = '';

		if ( ! empty( $fonts ) ) {
			foreach( $fonts as $index => $font ) {
				$count = $index + 1;
				$font_var = 'webfont-' . $count;
				$return_fonts .= sprintf( "// %s\n$%s: \"%s\";\n", $font->name, $font_var, $font->name );
			}
		}
		return $return_fonts;
	}

	protected function get_images( $image_data ) {

		$image_return = [];

		// Convert from JSON.
		$images = json_decode( $image_data );

		if ( ! empty( $images ) ) {
			foreach ( $images as $image ) {

				// Determine size arguments.
				$size_arg = '';
				$image_name = '';
				if ( property_exists( $image, 'width' ) && property_exists( $image, 'height' ) && $image->width && $image->height ) {
					$size_arg = sprintf( '%dx%d/', $image->width, $image->height );
					$image_name = sprintf( '%dx%d', $image->width, $image->height );
				} else {
					if ( property_exists( $image, 'width' ) && $image->width ) {
						$size_arg = sprintf( '%d/', $image->width );
						$image_name = sprintf( '%dx%d', $image->width, $image->width );
					}
					if ( property_exists( $image, 'height' ) && $image->height ) {
						$size_arg = sprintf( '%d/', $image->height );
						$image_name = sprintf( '%dx%d', $image->height, $image->height );
					}
				}

				// Image background color.
				$background_color_arg = '';
				if ( property_exists( $image, 'background' ) && $image->background ) {
					$background_color_arg = str_replace( '#', '', $image->background );
					$background_color_arg = htmlspecialchars( $background_color_arg ) . '/';
				}

				// Image text color.
				$text_color_arg = '';
				if ( property_exists( $image, 'text' ) && $image->text ) {
					$text_color_arg = str_replace( '#', '', $image->text );
					$text_color_arg = htmlspecialchars( $text_color_arg ) . '/';
				}

				// Image text.
				$label_arg = '';
				if ( property_exists( $image, 'name' ) && $image->name ) {
					$label_arg = '?text=' . urlencode( htmlspecialchars( $image->name ) );
				}

				if ( $size_arg ) {
					$image_return[] = [
						'name'  => $image_name,
						'src' => sprintf( 'https://via.placeholder.com/%s%s%s%s', $size_arg, $background_color_arg, $text_color_arg, $label_arg )
					];
				}
			}
		}

		return $image_return;
	}
}