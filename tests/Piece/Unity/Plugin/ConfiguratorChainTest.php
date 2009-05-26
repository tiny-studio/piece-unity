<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2007-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2007-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 0.11.0
 */

// {{{ Piece_Unity_Plugin_ConfiguratorChainTestCase

/**
 * Some tests for Piece_Unity_Plugin_ConfiguratorChain.
 *
 * @package    Piece_Unity
 * @copyright  2007-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.11.0
 */
class Piece_Unity_Plugin_ConfiguratorChainTestCase extends Piece_Unity_PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    public function setUp()
    {
        parent::setUp();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Piece_Unity_Plugin_Factory::addPluginDirectory(dirname(__FILE__) . '/' . basename(__FILE__, '.php'));
        Piece_Unity_Plugin_Factory::addPluginPrefix('');
    }

    /**
     * @test
     */
    public function invokeAConfigurator()
    {
        $config = new Piece_Unity_Config();
        $config->setExtension('ConfiguratorChain', 'configurators', array('FirstConfigurator'));
        $context = Piece_Unity_Context::singleton();
        $context->setConfiguration($config);

        $chain = new Piece_Unity_Plugin_ConfiguratorChain();
        $chain->invoke();
        $request = $context->getRequest();

        $this->assertTrue($request->hasParameter('FirstConfiguratorCalled'));
        $this->assertTrue($request->getParameter('FirstConfiguratorCalled'));
    }

    /**
     * @test
     */
    public function invokeMultipleConfigurators()
    {
        $config = new Piece_Unity_Config();
        $config->setExtension('ConfiguratorChain', 'configurators', array('FirstConfigurator', 'SecondConfigurator'));
        $context = Piece_Unity_Context::singleton();
        $context->setConfiguration($config);

        $chain = new Piece_Unity_Plugin_ConfiguratorChain();
        $chain->invoke();
        $request = $context->getRequest();

        $this->assertTrue($request->hasParameter('FirstConfiguratorCalled'));
        $this->assertTrue($request->getParameter('FirstConfiguratorCalled'));
        $this->assertTrue($request->hasParameter('SecondConfiguratorCalled'));
        $this->assertTrue($request->getParameter('SecondConfiguratorCalled'));

        $logs = $request->getParameter('logs');

        $this->assertEquals('FirstConfigurator', array_shift($logs));
        $this->assertEquals('SecondConfigurator', array_shift($logs));
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

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