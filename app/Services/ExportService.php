<?php

namespace App\Services;

use App\Project;
use Storage;

class ExportService {
	public function __construct() {}

	public function get( Project $data ) {

//		var_dump( class_exists( 'Storage' ) );
//		echo '<pre>' . print_r( $data->id, TRUE ) . '</pre>';

		$colors = $this->get_colors( $data->colors_defs );
		$fonts  = $this->get_fonts( $data->typekit_fonts, $data->google_fonts, $data->web_fonts );

		echo '<pre>' . print_r( Storage::disk('local')->put('test.scss', $colors.$fonts), TRUE ) . '</pre>';

		echo '<pre>' . print_r( $fonts . $colors, TRUE ) . '</pre>';

		$images = $this->get_images( $data->image_defs );
//		echo '<pre>' . print_r( $data, TRUE ) . '</pre>';
//		return 'EXPORT SERVICE';
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

		// Convert from JSON.
		$images = json_decode( $image_data );

		if ( ! empty( $images ) ) {
			foreach ( $images as $image ) {

				// Determine size arguments.
				$size_arg = '';
				if ( property_exists( $image, 'width' ) && property_exists( $image, 'height' ) && $image->width && $image->height ) {
					$size_arg = sprintf( '%dx%d/', $image->width, $image->height );
				} else {
					if ( property_exists( $image, 'width' ) && $image->width ) {
						$size_arg = sprintf( '%d/', $image->width );
					}
					if ( property_exists( $image, 'width' ) && $image->height ) {
						$size_arg = sprintf( '%d/', $image->height );
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
					echo '<div>';
					printf( 'https://via.placeholder.com/%s%s%s%s', $size_arg, $background_color_arg, $text_color_arg, $label_arg );
					echo '</div>';
//					echo '<pre>' . print_r( $image, TRUE ) . '</pre>';
				}
			}
		}
		
	}
}