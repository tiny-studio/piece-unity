<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006, KUBO Atsuhiro <iteman@users.sourceforge.net>
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
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://iteman.typepad.jp/piece/
 * @since      File available since Release 0.1.0
 */

require_once 'PEAR/PackageFileManager2.php';

PEAR::staticPushErrorHandling(PEAR_ERROR_CALLBACK, create_function('$error', 'var_dump($error); exit();'));

$version = '0.5.0';
$apiVersion = '0.5.0';
$notes = "This release includes a lot of enhancements and fixing two problems as follows:

<<< Enhancements >>>

Kernel:

* Piece_Unity
- invoke(): Changed the return value to null.

* Piece_Unity_Plugin_Common
- getExtension(): Changed code so as to immediately return when an extension is false or an array.
- Removed unused statements.

* Piece_Unity_Context
- Added setBasePath() method for setting the base path of the current request.
- Added setScriptName() method for setting the script name of the current request.
- Renamed the method from getBaseURLPath() to getBasePath().
- Renamed the method from getBaseURL() to getScriptName().

Plug-ins:

* Interceptor_ProxyBasePath
- An interceptor to adjust the base path and the script name of the current request which are held in the Piece_Unity_Context object. This interceptor is used and only works when your web servers are used as reverse proxies.

* Interceptor_SessionStart
- An interceptor to start session automatically.

* Root
- Removed code to start a session.

* OutputBufferStack
- Changed code so as to immediately return when the extension is not an array.
- Added error handling.

* InterceptorChain
- Changed the default value of 'interceptors' extension point to the array which only includes 'Interceptor_SessionStart' plug-in.
- Changed code so as to immediately return when the extension is not an array.
- Added error handling.

* KernelConfigurator
- Added 'pluginDirectories' configuration point for setting plug-in directories.
- Added error handling.

* View
- Added the current session name and the current session id as view elements __sessionName and __sessionID.

Example applications:

- Updated and improved.
- Changed the event names in the action states.

<<< Defect fixes >>>

Kernel:

* Piece_Unity_Context
- Fixed the problem that slashes are replaced with backslashes.
- Fixed the problem where the base path and the script name include the starting slash with Apache 2.0.58 on Windows.";

$package = new PEAR_PackageFileManager2();
$package->setOptions(array('filelistgenerator' => 'svn',
                           'changelogoldtonew' => false,
                           'simpleoutput'      => true,
                           'baseinstalldir'    => '/',
                           'packagefile'       => 'package2.xml',
                           'packagedirectory'  => '.')
                     );

$package->setPackage('Piece_Unity');
$package->setPackageType('php');
$package->setSummary('A stateful and secure MVC framework for PHP');
$package->setDescription('Piece_Unity is a stateful and secure MVC framework for PHP. Piece_Unity has two major features. The first one is a technology known as continuation server - It based on Piece_Flow web flow engine, flow control using it, and storing/restoring states. The second one is an Eclipse like plug-in system using extension points and configuration points.');
$package->setChannel('pear.hatotech.org');
$package->setLicense('BSD License (revised)',
                     'http://www.opensource.org/licenses/bsd-license.php'
                     );
$package->setAPIVersion($apiVersion);
$package->setAPIStability('beta');
$package->setReleaseVersion($version);
$package->setReleaseStability('beta');
$package->setNotes($notes);
$package->setPhpDep('4.3.0');
$package->setPearinstallerDep('1.4.3');
$package->addMaintainer('lead', 'iteman', 'KUBO Atsuhiro', 'iteman@users.sourceforge.net');
$package->addIgnore(array('package.php', 'package.xml', 'package2.xml'));
$package->addGlobalReplacement('package-info', '@package_version@', 'version');
$package->generateContents();
$package1 = &$package->exportCompatiblePackageFile1();

if (array_key_exists(1, $_SERVER['argv'])
    && $_SERVER['argv'][1] == 'make'
    ) {
    $package->writePackageFile();
    $package1->writePackageFile();
} else {
    $package->debugPackageFile();
    $package1->debugPackageFile();
}

exit();

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
