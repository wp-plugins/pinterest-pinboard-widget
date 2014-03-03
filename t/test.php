<?php

require_once(dirname(__FILE__) .'/simpletest/autorun.php');
require_once(dirname(__FILE__) .'/mock-wordpress.php');
require_once(dirname(__FILE__) .'/../pinterest-pinboard-widget.php');

/**
 * Unit tests for class: Pinterest_Pinboard
 */
class Pinterest_Pinboard_Tests extends UnitTestCase {
    
    function test_get_version() {
        $board = new Pinterest_Pinboard();
        $this->assertEqual($board->get_version(), '1.0.0');
    }

    function test_get_footer() {
        $board = new Pinterest_Pinboard();
        $this->assertPattern(
            '<!-- Version: 1.0.0 // Execution Time: .* \(ms\) -->',
            $footer = $board->get_footer()
        );
    }
    
    function test_get_pins_failure() {
        global $WP_ERROR;
        $WP_ERROR = true;
        $board = new Pinterest_Pinboard();
        $this->assertNull($board->get_pins('pinterest', 10), 'Expecting null');
    }
    
    function test_get_pins_success() {
        global $WP_ERROR;
        $WP_ERROR = false;        
        global $RSS_ITEMS;
        $RSS_ITEMS = array(
            new Mock_Rss_Item(
                'A title',
                'A description: <img src="http://codefish.nl/238690848970130635_0YplKTcQ_b.jpg">',
                'http://www.codefish.nl/'
            )
        );
        $board = new Pinterest_Pinboard();
        $pins = $board->get_pins('pinterest', 5);
        $this->assertNotNull($pins);
        $this->assertEqual(sizeof($pins), 1);
        $this->assertEqual($pins[0]['image'], '//codefish.nl/238690848970130635_0YplKTcQ_t.jpg');
    }
    
    /**
     * Test against a bug in version 1.0.1 that caused the parsing to go wrong
     * when there was a quote (") character in the RSS description.
     */
    function test_pins_with_quotes() {
        global $WP_ERROR;
        $WP_ERROR = false;
        global $RSS_ITEMS;
        $RSS_ITEMS = array(
            new Mock_Rss_Item(
                '"Books are a hard-bo',
                html_entity_decode('&lt;p&gt;&lt;a href="/pin/137500594843095531/"&gt;&lt;img src="http://media-cdn.pinterest.com/upload/238690848970130635_0YplKTcQ_b.jpg"&gt;&lt;/a&gt;&lt;/p&gt;&lt;p&gt;"Books are a hard-bound drug with no danger of an overdose. I am the happy victim of books" - Karl Lagerfeld&lt;/p&gt;'),
                'http://pinterest.com/pin/137500594843095531/'
            )
        );
        $board = new Pinterest_Pinboard();
        $pins = $board->get_pins('pinterest', 5);
        $this->assertNotNull($pins);
        $this->assertEqual(sizeof($pins), 1);
        $this->assertEqual($pins[0]['image'], '//media-cdn.pinterest.com/upload/238690848970130635_0YplKTcQ_t.jpg');
    }
    
}

/**
 * Unit tests for class: Pinterest_Pinboard_Widget
 */
class Pinterest_Pinboard_Widget_Tests extends UnitTestCase {
    
    function test_widget() {
        $args = array();
        $instance = array(
            username => 'pinterest'
        );
        $widget = new Pinterest_Pinboard_Widget();
        ob_start();
        $widget->widget($args, $instance);
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertNotNull($contents);
    }
    
}

?>
