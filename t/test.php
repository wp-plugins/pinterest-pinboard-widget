<?php

require_once(dirname(__FILE__) .'/simpletest/autorun.php');
require_once(dirname(__FILE__) .'/mock-wordpress.php');
require_once(dirname(__FILE__) .'/../pinterest-pinboard-widget.php');

/**
 * Unit tests for class: Pinterest_Pinboard_Widget
 */
 
class Pinterest_Pinboard_Widget_Tests extends UnitTestCase {
    
    function test_is_secure() {
        $widget = new Pinterest_Pinboard_Widget();
        $this->assertFalse($widget->is_secure());
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($widget->is_secure());
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertTrue($widget->is_secure());
    }
    
    function test_get_version() {
        $widget = new Pinterest_Pinboard_Widget();
        $this->assertEqual($widget->get_version(), '1.0.0');
    }
    
    function test_get_footer() {
        $widget = new Pinterest_Pinboard_Widget();
        $this->assertPattern(
            '<!-- Plugin ID: Pinterest-Pinboard-Widget // Version: 1.0.0 // Execution Time: .* \(ms\) -->',
            $footer = $widget->get_footer()
        );
        echo("footer: $footer");
    }

}

?>
