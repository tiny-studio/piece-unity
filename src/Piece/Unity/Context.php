<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
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
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      File available since Release 0.1.0
 */

// {{{ Piece_Unity_Context

/**
 * The application context holder for Piece_Unity applications.
 *
 * @package    Piece_Unity
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_Context
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

    private $_view;
    private $_eventName;
    private $_session;
    private $_eventNameImported = false;
    private $_eventNameKey = '_event';
    private $_scriptName;
    private $_originalScriptName;
    private $_basePath = '';
    private $_attributes = array();
    private $_proxyPath;
    private $_continuation;
    private $_appRootPath = '';

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ __construct()

    /**
     * Creates a Piece_Unity_Request object and sets an event to the context.
     */
    public function __construct()
    {
        $this->_session = new Piece_Unity_Session();
        $this->_scriptName = $this->_originalScriptName = Stagehand_HTTP_ServerEnv::getScriptName();
        $this->_basePath = $this->_getBasePath();
    }

    // }}}
    // {{{ setView()

    /**
     * Sets a view string. It will be dispatched to an appropriate renderer.
     *
     * @param string $view
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    // }}}
    // {{{ getView()

    /**
     * Gets the view string.
     *
     * @return string
     */
    public function getView()
    {
        return $this->_view;
    }

    // }}}
    // {{{ getEventName()

    /**
     * Gets the event name.
     *
     * @return string
     */
    public function getEventName()
    {
        if (!$this->_eventNameImported) {
            $this->_importEventNameFromSubmit();
        }

        if (!$this->_eventNameImported) {
            $this->_importEventNameFromRequest();
        }

        return $this->_eventName;
    }

    // }}}
    // {{{ getSession()

    /**
     * Gets the session state object.
     *
     * @return mixed
     */
    public function getSession()
    {
        return $this->_session;
    }

    // }}}
    // {{{ setEventNameKey()

    /**
     * Sets a key which represents the event name parameter.
     *
     * @param string $eventNameKey
     */
    public function setEventNameKey($eventNameKey)
    {
        $this->_eventNameKey = $eventNameKey;
    }

    // }}}
    // {{{ getEventNameKey()

    /**
     * Gets the key which represents the event name parameter.
     *
     * @return string
     */
    public function getEventNameKey()
    {
        return $this->_eventNameKey;
    }

    // }}}
    // {{{ setEventName()

    /**
     * Sets an event name for the current request.
     *
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->_eventNameImported = true;
        $this->_eventName = $eventName;
    }

    // }}}
    // {{{ getScriptName()

    /**
     * Gets the script name of the current request.
     *
     * @return string
     */
    public function getScriptName()
    {
        return $this->_scriptName;
    }

    // }}}
    // {{{ getOriginalScriptName()

    /**
     * Gets the original script name of the current request.
     *
     * @return string
     * @since Method available since Release 1.7.1
     */
    public function getOriginalScriptName()
    {
        return $this->_originalScriptName;
    }

    // }}}
    // {{{ getBasePath()

    /**
     * Gets the base path of the current request.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    // }}}
    // {{{ setScriptName()

    /**
     * Sets the script name of the current request.
     *
     * @param string $scriptName
     * @since Method available since Release 0.5.0
     */
    public function setScriptName($scriptName)
    {
        $this->_scriptName = $scriptName;
    }

    // }}}
    // {{{ setBasePath()

    /**
     * Sets the base path of the current request.
     *
     * @param string $basePath
     * @since Method available since Release 0.5.0
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = $basePath;
    }

    // }}}
    // {{{ setAttribute()

    /**
     * Sets an attribute for the current request.
     *
     * @param string $name
     * @param mixed  $value
     * @since Method available since Release 0.6.0
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    // }}}
    // {{{ setAttributeByRef()

    /**
     * Sets an attribute by reference for the current request.
     *
     * @param string $name
     * @param mixed  &$value
     * @since Method available since Release 0.6.0
     */
    public function setAttributeByRef($name, &$value)
    {
        $this->_attributes[$name] = &$value;
    }

    // }}}
    // {{{ hasAttribute()

    /**
     * Returns whether the current request has an attribute with a given name.
     *
     * @param string $name
     * @return boolean
     * @since Method available since Release 0.6.0
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->_attributes);
    }

    // }}}
    // {{{ getAttribute()

    /**
     * Gets an attribute for the current request.
     *
     * @param string $name
     * @return mixed
     * @since Method available since Release 0.6.0
     */
    public function &getAttribute($name)
    {
        return $this->_attributes[$name];
    }

    // }}}
    // {{{ removeAttribute()

    /**
     * Removes an attribute from the current request.
     *
     * @param string $name
     * @since Method available since Release 0.6.0
     */
    public function removeAttribute($name)
    {
        unset($this->_attributes[$name]);
    }

    // }}}
    // {{{ clearAttributes()

    /**
     * Removes all attributes from the current request.
     *
     * @since Method available since Release 0.6.0
     */
    public function clearAttributes()
    {
        $this->_attributes = array();
    }

    // }}}
    // {{{ setProxyPath()

    /**
     * Sets the proxy path if the application uses proxy servers.
     *
     * @param string $proxyPath
     * @since Method available since Release 0.6.0
     */
    public function setProxyPath($proxyPath)
    {
        $this->_proxyPath = $proxyPath;
    }

    // }}}
    // {{{ getProxyPath()

    /**
     * Gets the proxy path of the application.
     *
     * @return string
     * @since Method available since Release 0.6.0
     */
    public function getProxyPath()
    {
        return $this->_proxyPath;
    }

    // }}}
    // {{{ setContinuation()

    /**
     * Sets the Piece_Flow_Continuation object for the current session.
     *
     * @param Piece_Flow_Continuation $continuation
     * @since Method available since Release 0.6.0
     */
    public function setContinuation($continuation)
    {
        $this->_continuation = $continuation;
    }

    // }}}
    // {{{ getContinuation()

    /**
     * Gets the Piece_Flow_Continuation object for the current session.
     *
     * @return Piece_Flow_Continuation
     * @since Method available since Release 0.6.0
     */
    public function getContinuation()
    {
        return $this->_continuation;
    }

    // }}}
    // {{{ getValidation()

    /**
     * Gets the Piece_Unity_Validation object for the current request.
     *
     * @return Piece_Unity_Validation
     * @since Method available since Release 0.7.0
     */
    public function getValidation()
    {
        $this->validation->setContext($this);
        return $this->validation;
    }

    // }}}
    // {{{ setAppRootPath()

    /**
     * Sets the URI path that form the top of the document tree of an application
     * visible from the web.
     *
     * @param string $appRootPath
     * @since Method available since Release 0.12.0
     */
    public function setAppRootPath($appRootPath)
    {
        $this->_appRootPath = $appRootPath;
    }

    // }}}
    // {{{ getAppRootPath()

    /**
     * Gets the URI path that form the top of the document tree of an application
     * visible from the web.
     *
     * @return string
     * @since Method available since Release 0.12.0
     */
    public function getAppRootPath()
    {
        return $this->_appRootPath;
    }

    // }}}
    // {{{ removeProxyPath()

    /**
     * Removes the proxy path from a given URI Path.
     *
     * @param string $path
     * @return string
     * @since Method available since Release 1.5.0
     */
    public function removeProxyPath($path)
    {
        return preg_replace("!^{$this->_proxyPath}!", '', $path);
    }

    // }}}
    // {{{ sendHTTPStatus()

    /**
     * Sends a HTTP status line like "HTTP/1.1 404 Not Found".
     *
     * @param integer $statusCode
     * @since Method available since Release 1.5.0
     */
    public function sendHTTPStatus($statusCode)
    {
        Stagehand_HTTP_Status::send($statusCode);
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _importEventNameFromSubmit()

    /**
     * Imports an event name from the submit by a submit or an image.
     *
     * @since Method available since Release 0.9.0
     */
    private function _importEventNameFromSubmit()
    {
        $xFound = false;
        $yFound = false;
        foreach ($this->request->getParameters() as $key => $value) {
            if (preg_match("/^{$this->_eventNameKey}_(.+)$/", $key, $matches)) {
                $eventName = $matches[1];
                $lastTwoBytes = substr($matches[1], -2);
                if ($lastTwoBytes == '_x') {
                    $xFound = true;
                    $xEventName = substr($matches[1], 0, -2);
                    if ($yFound) {
                        break;
                    }
                } elseif ($lastTwoBytes == '_y') {
                    $yFound = true;
                    $yEventName = substr($matches[1], 0, -2);
                    if ($xFound) {
                        break;
                    }
                } else {
                    $this->setEventName($eventName);
                    return;
                }
            }
        }

        if ($xFound || $yFound) {
            if ($xFound && $yFound && $xEventName == $yEventName) {
                $this->setEventName($xEventName);
            } else {
                $this->setEventName($eventName);
            }
        }
    }

    // }}}
    // {{{ _importEventNameFromRequest()

    /**
     * Imports an event name from the request parameters.
     *
     * @since Method available since Release 0.9.0
     */
    private function _importEventNameFromRequest()
    {
        $eventName = $this->request->hasParameter($this->_eventNameKey) ? $this->request->getParameter($this->_eventNameKey) : null;
        $this->setEventName($eventName);
    }

    // }}}
    // {{{ _getBasePath()

    /**
     * Gets the base path from the script name.
     *
     * @return string
     * @since Method available since Release 1.7.1
     */
    private function _getBasePath()
    {
        $positionOfSlash = strrpos($this->_scriptName, '/');
        if (!$positionOfSlash) {
            return $this->_scriptName;
        }

        return substr($this->_scriptName, 0, $positionOfSlash);
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
