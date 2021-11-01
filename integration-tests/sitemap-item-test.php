<?php
/**
 * Yoast SEO: News plugin test file.
 *
 * @package WPSEO_News\Tests
 */

/**
 * Class WPSEO_News_Sitemap_Item_Test.
 */
class WPSEO_News_Sitemap_Item_Test extends WPSEO_News_UnitTestCase {

	/**
	 * Checks if the time output for the sitemap is correct when there is a post_date_gmt set.
	 *
	 * @covers WPSEO_News_Sitemap_Item::get_publication_date
	 */
	public function test_get_publication_date_returning_correct_UTC_time() {
		$base_time       = time();
		$timezone_format = DateTime::W3C;

		$test_post_date_gmt = self::factory()->post->create_and_get(
			[
				'post_title'    => 'Newest post',
				'post_date'     => gmdate( $timezone_format, $base_time ),
				'post_date_gmt' => gmdate( $timezone_format, $base_time ),
				'post_type'     => 'post',
			]
		);
		$publication_tag  = "\t\t<news:publication>\n";
		$publication_tag .= "\t\t\t<news:name>Test News Site</news:name>\n";
		$publication_tag .= "\t\t\t<news:language>en_GB</news:language>\n";
		$publication_tag .= "\t\t</news:publication>\n";

		$instance = new WPSEO_News_Sitemap_Item_Double( $test_post_date_gmt , $publication_tag );

		$original_post_utc_time      = gmdate( $timezone_format, $base_time );
		$get_publication_date_output = $instance->get_publication_date( $test_post_date_gmt );

		// Check if post_date_gmt is the same as the output of get_publication_date().
		$this->assertSame( $original_post_utc_time, $get_publication_date_output );
	}

	/**
	 * Checks if the time output for the sitemap is correct when there is no post_date_gmt set.
	 *
	 * Prior to PHP 5.5.10, timezone offsets were not supported by `DateTimeZone` causing the test to fail.
	 *
	 * @requires PHP 5.5.10
	 *
	 * @covers WPSEO_News_Sitemap_Item::get_publication_date
	 */
	public function test_get_publication_date_returning_correct_post_date_when_no_gmt_set() {
		$base_time       = time();
		$timezone_format = DateTime::W3C;

		$test_post_date = self::factory()->post->create_and_get(
			[
				'post_title'    => 'Newest post',
				'post_date'     => gmdate( $timezone_format, $base_time ),
				'post_type'     => 'post',
			]
		);

		// Manually set post_date_gmt to an invalid string, because at creation WP forces a valid string.
		$test_post_date->post_date_gmt = 'This is an invalid string';

		$publication_tag  = "\t\t<news:publication>\n";
		$publication_tag .= "\t\t\t<news:name>Test News Site</news:name>\n";
		$publication_tag .= "\t\t\t<news:language>en_GB</news:language>\n";
		$publication_tag .= "\t\t</news:publication>\n";

		$instance = new WPSEO_News_Sitemap_Item_Double( $test_post_date, $publication_tag );

		$original_post_date          = '';
		$get_publication_date_output = $instance->get_publication_date( $test_post_date );

		// Check if post_date is the same as the output of get_publication_date().
		$this->assertSame( $original_post_date, $get_publication_date_output );
	}

	/**
	 * Checks if the time output for the sitemap is an empty string when dates are
	 * invalid.
	 *
	 * @covers WPSEO_News_Sitemap_Item::get_publication_date
	 */
	public function test_get_publication_date_with_invalid_datetime() {
		$test_post_date_gmt = self::factory()->post->create_and_get(
			[
				'post_title'    => 'Newest post',
				'post_type'     => 'post',
			]
		);

		$test_post_date_gmt->post_date     = -10;
		$test_post_date_gmt->post_date_gmt = -10;

		$publication_tag  = "\t\t<news:publication>\n";
		$publication_tag .= "\t\t\t<news:name>Test News Site</news:name>\n";
		$publication_tag .= "\t\t\t<news:language>en_GB</news:language>\n";
		$publication_tag .= "\t\t</news:publication>\n";

		$instance = new WPSEO_News_Sitemap_Item_Double( $test_post_date_gmt, $publication_tag );

		$get_publication_date_output = $instance->get_publication_date( $test_post_date_gmt );

		// Check if post_date_gmt is the same as the output of get_publication_date().
		$this->assertSame( '', $get_publication_date_output );
	}

	/**
	 * Checks if the post title output for the sitemap defaults to the post title + separator + blogtitle
	 * when no SEO title is present.
	 *
	 * @covers WPSEO_News_Sitemap_Item::get_item_title
	 */
	public function test_get_item_title_when_no_seo_title_set() {
		$post_details   = [
			'post_title' => 'Post without SEO title',
			'post_type'  => 'post',
		];
		$test_seo_title = self::factory()->post->create_and_get( $post_details );

		$publication_tag  = "\t\t<news:publication>\n";
		$publication_tag .= "\t\t\t<news:name>Test News Site</news:name>\n";
		$publication_tag .= "\t\t\t<news:language>en_GB</news:language>\n";
		$publication_tag .= "\t\t</news:publication>\n";

		$instance     = new WPSEO_News_Sitemap_Item_Double( $test_seo_title, $publication_tag );
		$title_output = $instance->get_item_title( $test_seo_title );

		// Check if correct post_title - blogname is returned.
		$this->assertSame( 'Post without SEO title', $title_output );
	}

	/**
	 * Checks if the post title output for the sitemap exits and returns an empty string when given null as a value.
	 *
	 * @covers WPSEO_News_Sitemap_Item::get_item_title
	 */
	public function test_get_item_title_when_post_is_null() {
		$post_details   = [
			'post_title' => 'title',
			'post_type'  => 'post',
		];
		$test_seo_title = self::factory()->post->create_and_get( $post_details );

		$publication_tag  = "\t\t<news:publication>\n";
		$publication_tag .= "\t\t\t<news:name>Test News Site</news:name>\n";
		$publication_tag .= "\t\t\t<news:language>en_GB</news:language>\n";
		$publication_tag .= "\t\t</news:publication>\n";

		$instance = new WPSEO_News_Sitemap_Item_Double( $test_seo_title, $publication_tag );

		$title_output = $instance->get_item_title( null );

		// Check if an empty string is correctly returned.
		$this->assertSame( '', $title_output );
	}

	/**
	 * Checks if the post title output for the sitemap is the SEO title when set.
	 *
	 * @covers WPSEO_News_Sitemap_Item::get_item_title
	 */
	public function test_get_item_title_when_seo_title_set() {
		$post_details   = [
			'post_title' => 'Post with SEO title',
			'post_type'  => 'post',
		];
		$test_seo_title = self::factory()->post->create_and_get( $post_details );

		$publication_tag  = "\t\t<news:publication>\n";
		$publication_tag .= "\t\t\t<news:name>Test News Site</news:name>\n";
		$publication_tag .= "\t\t\t<news:language>en_GB</news:language>\n";
		$publication_tag .= "\t\t</news:publication>\n";

		$instance       = new WPSEO_News_Sitemap_Item_Double( $test_seo_title, $publication_tag );

		$title_output = $instance->get_item_title( $test_seo_title );

		// Check if correct post_title is returned.
		$this->assertSame( 'Post with SEO title', $title_output );
	}
}

