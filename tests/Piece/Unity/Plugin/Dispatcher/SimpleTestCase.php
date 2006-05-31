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
 * @see        Piece_Unity_Plugin_Dispatcher_Simple
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Unity/Plugin/Dispatcher/Simple.php';

require_once 'Piece/Unity/Request.php';
require_once 'Piece/Unity/Config.php';

// {{{ Piece_Unity_Plugin_Dispatcher_SimpleTestCase

/**
 * TestCase for Piece_Unity_Plugin_Dispatcher_Simple
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * @copyright  2006 KUBO Atsuhiro <iteman2002@yahoo.co.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://iteman.typepad.jp/piece/
 * @see        Piece_Unity_Plugin_Dispatcher_Simple
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_Plugin_Dispatcher_SimpleTestCase extends PHPUnit_TestCase
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

    function testDispatchingWithoutAction()
    {
        $request = &new Piece_Unity_Request();
        $request->setParameter('event', 'foo');
        $context = &Piece_Unity_Context::singleton();
        $context->setRequest($request);
        $config = &new Piece_Unity_Config();
        $context->setConfiguration($config);
        $dispatcher = &new Piece_Unity_Plugin_Dispatcher_Simple();
        $view = $dispatcher->invoke();

        $this->assertEquals('foo', $view);
    }

    function testDispatchingWithAction()
    {
        $GLOBALS['actionCalled'] = false;

        $request = &new Piece_Unity_Request();
        $request->setParameter('event', 'Example');
        $context = &Piece_Unity_Context::singleton();
        $context->setRequest($request);
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Piece_Unity_Plugin_Dispatcher_Simple', 'actionPath', dirname(__FILE__));
        $context->setConfiguration($config);
        $dispatcher = &new Piece_Unity_Plugin_Dispatcher_Simple();
        $view = $dispatcher->invoke();

        $this->assertEquals('Example', $view);
        $this->assertTrue($GLOBALS['actionCalled']);

        unset($GLOBALS['actionCalled']);
    }

    function testRelativePathVulnerability()
    {
        $GLOBALS['actionCalled'] = false;
        $GLOBALS['ExternalActionLoaded'] = false;

        $request = &new Piece_Unity_Request();
        $request->setParameter('event', '../External');
        $context = &Piece_Unity_Context::singleton();
        $context->setRequest($request);
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Piece_Unity_Plugin_Dispatcher_Simple', 'actionPath', dirname(__FILE__));
        $context->setConfiguration($config);
        $dispatcher = &new Piece_Unity_Plugin_Dispatcher_Simple();
        $view = $dispatcher->invoke();

        $this->assertEquals('../External', $view);
        $this->assertFalse($GLOBALS['actionCalled']);
        $this->assertFalse($GLOBALS['ExternalActionLoaded']);

        unset($GLOBALS['actionCalled']);
        unset($GLOBALS['ExternalActionLoaded']);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

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