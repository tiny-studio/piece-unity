<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Unity/Plugin/Common.php';
require_once 'Piece/Unity/Error.php';
require_once 'Piece/Unity/URI.php';

// {{{ Piece_Unity_Plugin_View

/**
 * A view handler which creates built-in view elements and renders view
 * elements with an appropriate renderer.
 *
 * @package    Piece_Unity
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_Plugin_View extends Piece_Unity_Plugin_Common
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

    // }}}
    // {{{ invoke()

    /**
     * Invokes the plugin specific code.
     */
    function invoke()
    {

        /*
         * Sets the Piece_Unity_Request object and the
         * Piece_Unity_Session object as built-in view elements.
         */
        $viewElement = &$this->_context->getViewElement();
        $request = &$this->_context->getRequest();
        $viewElement->setElementByRef('__request', $request);
        $session = &$this->_context->getSession();
        $viewElement->setElementByRef('__session', $session);
        $viewElement->setElement('__eventNameKey', $this->_context->getEventNameKey());
        $viewElement->setElement('__scriptName', $this->_context->getScriptName());
        $viewElement->setElement('__basePath', $this->_context->getBasePath());
        $viewElement->setElement('__sessionName', session_name());
        $viewElement->setElement('__sessionID', session_id());
        $viewElement->setElement('__appRootPath', $this->_context->getAppRootPath());
        $uri = &new Piece_Unity_URI();
        $viewElement->setElementByRef('__url', $uri); // deprecated
        $viewElement->setElementByRef('__uri', $uri);

        /*
         * Overwrites the current view with another one which is specified by
         * forcedView configuration.
         */
        $forcedView = $this->_getConfiguration('forcedView');
        if (!is_null($forcedView)) {
            $this->_context->setView($forcedView);
        }

        $config = &$this->_context->getConfiguration();
        $rendererExtension = $config->getExtension('View', 'renderer');
        if (strlen($rendererExtension)) {
            $config->setConfiguration('ViewSchemeHandler',
                                      'html',
                                      $rendererExtension
                                      );
        }

        $viewSchemeHandler = &$this->_getExtension('viewSchemeHandler');
        if (Piece_Unity_Error::hasErrors()) {
            return;
        }

        $rendererExtension = $viewSchemeHandler->invoke();
        if (Piece_Unity_Error::hasErrors()) {
            return;
        }

        $config->setExtension('View', 'renderer', $rendererExtension);
        $renderer = &$this->_getExtension('renderer');
        if (Piece_Unity_Error::hasErrors()) {
            return;
        }

        $renderer->invoke();
    }

    /**#@-*/

    /**#@+
     * @access private
     */
 
    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes extension points and configuration points.
     *
     * @since Method available since Release 0.6.0
     */
    function _initialize()
    {
        $this->_addExtensionPoint('renderer');
        $this->_addConfigurationPoint('forcedView');
        $this->_addExtensionPoint('viewSchemeHandler', 'ViewSchemeHandler');
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