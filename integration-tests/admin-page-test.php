<?php
/**
 * Yoast SEO: News plugin test file.
 *
 * @package WPSEO_News\Tests
 */

/**
 * Class WPSEO_News_Admin_Page_Test.
 */
class WPSEO_News_Admin_Page_Test extends WPSEO_News_UnitTestCase {

	/**
	 * Instance of the WPSEO_News_Admin_Page class.
	 *
	 * @var WPSEO_News_Admin_Page
	 */
	private $instance;

	/**
	 * Setting up the instance of WPSEO_News_Admin_Page.
	 */
	public function setUp() {
		parent::setUp();

		// Because is a global $wpseo_admin_pages we have to fill this one with an instance of WPSEO_Admin_Pages.
		global $wpseo_admin_pages;

		if ( empty( $wpseo_admin_pages ) ) {
			$wpseo_admin_pages = new WPSEO_Admin_Pages();
		}

		$this->instance = new WPSEO_News_Admin_Page();
	}

	/**
	 * Tests whether the admin page is generated correctly.
	 *
	 * @covers WPSEO_News_Admin_Page::display
	 */
	public function test_display() {

		// Start buffering to get the output of display method.
		ob_start();

		$this->instance->display();

		$output = ob_get_contents();
		ob_end_clean();

		// We expect this part in the generated HTML.
		$expected_output = <<<'EOT'
<p>You will generally only need a News Sitemap when your website is included in Google News.</p><p><a target="_blank" href="http://example.org/news-sitemap.xml">View your News Sitemap</a>.</p>
EOT;

		// Check if the $output contains the $expected_output.
		$this->assertStringContainsString( $expected_output, $output );
	}
}
