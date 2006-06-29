<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006, KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * @copyright  2006 KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://iteman.typepad.jp/piece/
 * @see        Piece_Unity_Plugin_Renderer_Smarty
 * @since      File available since Release 0.2.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity/Plugin/Renderer/Smarty.php';
require_once 'Piece/Unity/Context.php';
// require_once 'Piece/Unity/Config.php';
// require_once 'Piece/Unity/Plugin/Dispatcher/Simple.php';
// require_once 'Piece/Unity/Plugin/View.php';

// {{{ Piece_Unity_Plugin_Renderer_SmartyTestCase

/**
 * TestCase for Piece_Unity_Plugin_Renderer_Smarty
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * @copyright  2006 KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @see        Piece_Unity_Plugin_Renderer_Smarty
 * @since      Class available since Release 0.2.0
 */
class Piece_Unity_Plugin_Renderer_SmartyTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Unity_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    function tearDown()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->clear();
        Piece_Unity_Error::clearErrors();
        unset($_GET['_event']);
        unset($_SERVER['REQUEST_METHOD']);
        Piece_Unity_Error::popCallback();
    }

    function testRendering()
    {
        $viewString = 'SmartyExample';
        $_GET['_event'] = $viewString;

        $this->assertEquals("This is a test for rendering dynamic pages.\nThis is a dynamic content.", $this->_render());

        $smarty = &new Smarty();
        $smarty->compile_dir = dirname(__FILE__) . '/';
        $smarty->clear_compiled_tpl("$viewString.tpl");
    }

    function testRelativePathVulnerability()
    {
        $_GET['_event'] = '../RelativePathVulnerability';

        $this->assertEquals('', $this->_render());
    }

    function testKeepingReference()
    {
        $viewString = 'SmartyKeepingReference';
        $context = &Piece_Unity_Context::singleton();

        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Renderer_Smarty', 'templateDirectory', dirname(__FILE__));
        $config->setConfiguration('Renderer_Smarty', 'compileDirectory', dirname(__FILE__));
        $context->setConfiguration($config);

        $foo = &new stdClass();
        $viewElement = &$context->getViewElement();
        $viewElement->setElementByRef('foo', $foo);
        $context->setView($viewString);

        $renderer = &new Piece_Unity_Plugin_Renderer_Smarty();
        ob_start();
        $renderer->invoke();
        ob_end_clean();

        $this->assertTrue(array_key_exists('bar', $foo));
        $this->assertEquals('baz', $foo->bar);

        $smarty = &new Smarty();
        $smarty->compile_dir = dirname(__FILE__) . '/';
        $smarty->clear_compiled_tpl("$viewString.tpl");
    }

    function testBuiltinElements()
    {
        $viewString = 'SmartyBuiltinElements';
        $context = &Piece_Unity_Context::singleton();

        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Renderer_Smarty', 'templateDirectory', dirname(__FILE__));
        $config->setConfiguration('Renderer_Smarty', 'compileDirectory', dirname(__FILE__));
        $config->setExtension('View', 'renderer', 'Renderer_Smarty');
        $context->setConfiguration($config);
        $context->setConfiguration($config);

        $foo = &new stdClass();
        $viewElement = &$context->getViewElement();
        $viewElement->setElementByRef('foo', $foo);
        $context->setView($viewString);

        $view = &new Piece_Unity_Plugin_View();
        ob_start();
        $view->invoke();
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('OK', $buffer);

        $smarty = &new Smarty();
        $smarty->compile_dir = dirname(__FILE__) . '/';
        $smarty->clear_compiled_tpl("$viewString.tpl");
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function _render()
    {
        $context = &Piece_Unity_Context::singleton();

        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Dispatcher_Simple', 'actionDirectory', dirname(__FILE__));
        $config->setConfiguration('Renderer_Smarty', 'templateDirectory', dirname(__FILE__));
        $config->setConfiguration('Renderer_Smarty', 'compileDirectory', dirname(__FILE__));
        $context->setConfiguration($config);

        $dispatcher = &new Piece_Unity_Plugin_Dispatcher_Simple();
        $context->setView($dispatcher->invoke());
        $renderer = &new Piece_Unity_Plugin_Renderer_Smarty();

        ob_start();
        $renderer->invoke();
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
?>
