<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity
 * @since      File available since Release 0.1.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity.php';
require_once 'Piece/Unity/Context.php';
require_once 'Cache/Lite/File.php';
require_once 'Piece/Unity/Config.php';
require_once 'Piece/Unity/Error.php';

// {{{ Piece_UnityTestCase

/**
 * TestCase for Piece_Unity
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity
 * @since      Class available since Release 0.1.0
 */
class Piece_UnityTestCase extends PHPUnit_TestCase
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
    }

    function tearDown()
    {
        unset($_SESSION);
        $cache = &new Cache_Lite_File(array('cacheDir' => dirname(__FILE__) . '/',
                                            'masterFile' => '',
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );
        $cache->clean();
        Piece_Unity_Context::clear();
        Piece_Unity_Error::clearErrors();
        Piece_Unity_Error::popCallback();
    }

    function testConfiguration()
    {
        $dynamicConfig = &new Piece_Unity_Config();
        $dynamicConfig->setExtension($GLOBALS['PIECE_UNITY_Root_Plugin'],
                                     'renderer',
                                     'Foo'
                                     );
        $dynamicConfig->setConfiguration($GLOBALS['PIECE_UNITY_Root_Plugin'],
                                         'Bar',
                                         'Baz'
                                         );
        $unity = &new Piece_Unity(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__),
                                  $dynamicConfig
                                  );
        $context = &Piece_Unity_Context::singleton();
        $config = &$context->getConfiguration();

        $this->assertEquals('Foo',
                            $config->getExtension($GLOBALS['PIECE_UNITY_Root_Plugin'], 'renderer')
                            );
        $this->assertEquals('Baz',
                            $config->getConfiguration($GLOBALS['PIECE_UNITY_Root_Plugin'], 'Bar')
                            );
    }

    function testConfigurationWithCacheDirectory()
    {
        $dynamicConfig = &new Piece_Unity_Config();
        $dynamicConfig->setExtension($GLOBALS['PIECE_UNITY_Root_Plugin'],
                                     'renderer',
                                     'Foo'
                                     );
        $dynamicConfig->setConfiguration($GLOBALS['PIECE_UNITY_Root_Plugin'],
                                     'Bar',
                                     'Baz'
                                     );
        $unity = &new Piece_Unity(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__),
                                  $dynamicConfig
                                  );
        $context = &Piece_Unity_Context::singleton();
        $config = &$context->getConfiguration();

        $masterFile = dirname(__FILE__) . '/../../data/piece-unity-config.yaml';
        $cache = &new Cache_Lite_File(array('cacheDir' => dirname(__FILE__) . '/',
                                            'masterFile' => $masterFile,
                                            'automaticSerialization' => true)
                                      );
        $config = $cache->get($masterFile);

        $this->assertTrue(is_a($config, 'Piece_Unity_Config'));
    }

    function testConfigurationWithoutDynamicConfiguration()
    {
        $unity = &new Piece_Unity(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__),
                                  null
                                  );
        $context = &Piece_Unity_Context::singleton();
        $config = &$context->getConfiguration();

        $this->assertTrue(is_a($config, 'Piece_Unity_Config'));
    }

    function testDispatch()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['_event'] = 'foo';

        $config = &new Piece_Unity_Config();
        $config->setExtension($GLOBALS['PIECE_UNITY_Root_Plugin'],
                              'dispatcher',
                              'Dispatcher_Simple'
                              );
        $unity = &new Piece_Unity(dirname(__FILE__) . '/../../data',
                                  dirname(__FILE__),
                                  $config
                                  );
        $unity->dispatch();
        $context = &Piece_Unity_Context::singleton();

        $this->assertEquals('foo', $context->getView());
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
