<?php
/**
 * Tests for the Jetpack_Sitemap_Librarian class.
 *
 * @package Jetpack
 * @since 4.7.0
 */

require dirname( __FILE__ ) . '/../../../../modules/sitemaps/sitemap-constants.php';
require dirname( __FILE__ ) . '/../../../../modules/sitemaps/sitemap-librarian.php';

/**
 * Test class for Jetpack_Sitemap_Librarian.
 *
 * @since 4.7.0
 */
class WP_Test_Jetpack_Sitemap_Librarian extends WP_UnitTestCase {

	/**
	 * Constructor does not throw a fatal error.
	 *
	 * @covers Jetpack_Sitemap_Librarian::__construct
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_constructor() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$this->assertTrue( true );
		return;
	}

	/**
	 * Getting an unset row returns null.
	 *
	 * @covers Jetpack_Sitemap_Librarian::read_sitemap_data
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_get_unset_row_is_null() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$row = $librarian->read_sitemap_data( 'unset', 'unset' );
		$this->assertTrue( is_null( $row ) );
		return;
	}

	/**
	 * Deleting an unset row returns false.
	 *
	 * @covers Jetpack_Sitemap_Librarian::delete_sitemap_data
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_delete_unset_row_returns_false() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$result = $librarian->delete_sitemap_data( 'unset', 'unset' );
		$this->assertEquals( false, $result );
		return;
	}

	/**
	 * Deleting a set row returns true.
	 *
	 * @covers Jetpack_Sitemap_Librarian::delete_sitemap_data
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_delete_set_row_returns_true() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$librarian->store_sitemap_data( 'name', 'type', 'contents', '1970-01-01 00:00:00' );
		$result = $librarian->delete_sitemap_data( 'name', 'type' );
		$this->assertEquals( true, $result );
		return;
	}

	/**
	 * Getting a set row is the identity(ish).
	 *
	 * @covers Jetpack_Sitemap_Librarian::store_sitemap_data
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_set_then_get_is_identity() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$contents = esc_html( 'These are the times that try men\'s souls.' );

		// Store a sitemap.
		$librarian->store_sitemap_data( 'name', 'type', $contents, '1970-01-01 00:00:00' );

		// Get the stored sitemap.
		$result = $librarian->read_sitemap_data( 'name', 'type' );

		// Check that the stored sitemap and the retrieved sitemap are the same.
		$this->assertEquals( 'name', $result['name'] );
		$this->assertEquals( 'type', $result['type'] );
		$this->assertEquals( $contents, $result['text'] );
		$this->assertEquals( '1970-01-01 00:00:00', $result['timestamp'] );

		// Clean up.
		$librarian->delete_sitemap_data( 'name', 'type' );
		return;
	}

	/**
	 * Setting the same name/type twice overwrites old data.
	 *
	 * @covers Jetpack_Sitemap_Librarian::store_sitemap_data
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_set_then_set_overwrites_data() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$old_contents = esc_html( 'It was the best of times,' );
		$new_contents = esc_html( 'It was the worst of times.' );

		// Set the same data twice.
		$librarian->store_sitemap_data( 'name', 'type', $old_contents, '1970-01-01 00:00:00' );
		$librarian->store_sitemap_data( 'name', 'type', $new_contents, '1980-01-01 00:00:00' );

		// Get the stored data.
		$result = $librarian->read_sitemap_data( 'name', 'type' );

		// Check that the second set is what comes out.
		$this->assertEquals( $new_contents, $result['text'] );
		$this->assertEquals( '1980-01-01 00:00:00', $result['timestamp'] );

		// Clean up.
		$librarian->delete_sitemap_data( 'name', 'type' );
		return;
	}

	/**
	 * Getting the text of a set row is the identity.
	 *
	 * @covers Jetpack_Sitemap_Librarian::get_sitemap_text
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_set_then_get_text_is_identity() {
		$librarian = new Jetpack_Sitemap_Librarian();
		$contents = 'These are the times that try men\'s souls.';

		// Store a sitemap.
		$librarian->store_sitemap_data( 'name', 'type', $contents, '1970-01-01 00:00:00' );

		// Get the text of the stored sitemap.
		$result = $librarian->get_sitemap_text( 'name', 'type' );

		// Check that the stored sitemap and the retrieved sitemap are the same.
		$this->assertEquals( $contents, $result );

		// Clean up.
		$librarian->delete_sitemap_data( 'name', 'type' );
		return;
	}

	/**
	 * Delete contiguously named rows.
	 *
	 * @covers Jetpack_Sitemap_Librarian::delete_numbered_sitemap_rows_after
	 * @group jetpack-sitemap
	 * @since 4.7.0
	 */
	public function test_sitemap_librarian_delete_contiguously_named_rows() {
		$librarian = new Jetpack_Sitemap_Librarian();

		// Store some contiguous data.
		$librarian->store_sitemap_data( 'name-1', JP_PAGE_SITEMAP_TYPE, 'foo', '1970-01-01 00:00:00' );
		$librarian->store_sitemap_data( 'name-2', JP_PAGE_SITEMAP_TYPE, 'foo', '1970-01-01 00:00:00' );
		$librarian->store_sitemap_data( 'name-3', JP_PAGE_SITEMAP_TYPE, 'foo', '1970-01-01 00:00:00' );

		// Delete it.
		$librarian->delete_numbered_sitemap_rows_after( 0, 'type' );

		// It's gone!
		$this->assertTrue( is_null( $librarian->read_sitemap_data( 'name-1', 'type' ) ) );
		$this->assertTrue( is_null( $librarian->read_sitemap_data( 'name-2', 'type' ) ) );
		$this->assertTrue( is_null( $librarian->read_sitemap_data( 'name-3', 'type' ) ) );
		return;
	}

}
