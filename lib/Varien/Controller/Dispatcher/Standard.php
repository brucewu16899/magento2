<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Controller
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Varien
 * @package    Varien_Controller
 */

/** Zend_Controller_Dispatcher_Abstract */
require_once 'Zend/Controller/Dispatcher/Abstract.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Zend
 * @category   Varien
 * @package    Varien_Controller
 */
class Varien_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Abstract
{
    /**
     * Current dispatchable directory
     * @var string
     */
    protected $_curDirectory;


    /**
     * Default module name
     *
     * @var string
     */
    protected $_defaultModuleName = 'default';


    /**
     * Current module (formatted)
     * @var string
     */
    protected $_curModule = 'default';

    /**
     * Directories where Zend_Controller_Action files are stored.
     * @var array
     */
    protected $_directories = array();

    /**
     * Add a single path to the controller directory stack
     *
     * @param string $path
     * @param string $module
     * @return Zend_Controller_Dispatcher_Standard
     */
    public function addControllerDirectory($path, $module = '')
    {
        if (empty($module)) {
            $module = $this->getDefaultModuleName();
        }
        $this->getFrontController()->addControllerDirectory($path, $module);
        return $this;
    }

    /**
     * Set controller directory
     *
     * @param array|string $directory
     * @return Zend_Controller_Dispatcher_Standard
     */
    public function setControllerDirectory($directory)
    {
        $this->getFrontController()->setControllerDirectory($directory);
        return $this;
    }

    /**
     * Return the currently set directories for Zend_Controller_Action class
     * lookup
     *
     * If a module is specified, returns just that directory.
     *
     * @param  string $module Module name
     * @return array|string Returns array of all directories by default, single
     * module directory if module argument provided
     */
    public function getControllerDirectory($module = null)
    {
        $directories = $this->getFrontController()->getControllerDirectory();

        if ((null !== $module) && (isset($this->_directories['module']))) {
            return $directories[$module];
        }

        return $directories;
    }

    /**
     * Format the module name.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatModuleName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted));
    }

    /**
     * Convert a class name to a filename
     *
     * @param string $class
     * @return string
     */
    public function classToFilename($class)
    {
        return str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be
     * dispatched to a controller.
     *
     * Use this method wisely. By default, the dispatcher will fall back to the
     * default controller (either in the module specified or the global default)
     * if a given controller does not exist. This method returning false does
     * not necessarily indicate the dispatcher will not still dispatch the call.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        $className = $this->getControllerClass($request);
        if (!$className) {
            return true;
        }

        $fileSpec    = $this->classToFilename($className);
        $dispatchDir = $this->getDispatchDirectory();
        $test        = $dispatchDir . DIRECTORY_SEPARATOR . $fileSpec;
        return Zend_Loader::isReadable($test);
    }

    /**
     * Dispatch to a controller/action
     *
     * By default, if a controller is not dispatchable, dispatch() will throw
     * an exception. If you wish to use the default controller instead, set the
     * param 'useDefaultControllerAlways' via {@link setParam()}.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return boolean
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            if (!$this->getParam('useDefaultControllerAlways')) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request->getControllerName() . ')');
            }

            $className = $this->getDefaultControllerClass($request);
        } else {
            $className = $this->getControllerClass($request);
            if (!$className) {
                $className = $this->getDefaultControllerClass($request);
            }
        }

        /**
         * Load the controller class file
         */
        $className = $this->loadClass($className);

        /**
         * Instantiate controller with request, response, and invocation
         * arguments; throw exception if it's not an action controller
         */
        $controller = new $className($request, $this->getResponse(), $this->getParams());
        if (!$controller instanceof Zend_Controller_Action) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception("Controller '$className' is not an instance of Zend_Controller_Action");
        }

        /**
         * Retrieve the action name
         */
        $action = $this->getActionMethod($request);

        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);
        $controller->dispatch($action);

        // Destroy the page controller instance and reflection objects
        $controller = null;
    }

    /**
     * Load a controller class
     *
     * Attempts to load the controller class file from
     * {@link getControllerDirectory()}.  If the controller belongs to a
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className
     * @return string Class name loaded
     * @throws Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className)
    {
        $dispatchDir = $this->getDispatchDirectory();

        $loadFile    = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);
        $dir         = dirname($loadFile);
        $file        = basename($loadFile);
        Zend_loader::loadFile($file, $dir, true);

        if ('default' != $this->_curModule) {
            $className = $this->formatModuleName($this->_curModule) . '_' . $className;
        }

        if (!class_exists($className)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $className . '")');
        }

        return $className;
    }

    /**
     * Get controller class name
     *
     * Try request first; if not found, try pulling from request parameter;
     * if still not found, fallback to default
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string|false Returns class name on success
     */
    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $controllerName = $request->getControllerName();
        if (empty($controllerName)) {
            return false;
        }

        $className = $this->formatControllerName($controllerName);

        $controllerDirs      = $this->getControllerDirectory();
        $this->_curModule    = $this->getDefaultModuleName();
        $this->_curDirectory = $controllerDirs[$this->getDefaultModuleName()];
        $module = $request->getModuleName();
        if ($this->isValidModule($module)) {
            $this->_curModule    = $module;
            $this->_curDirectory = $controllerDirs[$module];
        }

        return $className;
    }

    /**
     * Determine if a given module is valid
     *
     * @param string $module
     * @return bool
     */
    public function isValidModule($module)
    {
        $controllerDir = $this->getControllerDirectory();
        return ((null !== $module) && ('default' != $module) && isset($controllerDir[$module]));
    }

    /**
     * Retrieve default controller class
     *
     * Determines whether the default controller to use lies within the
     * requested module, or if the global default should be used.
     *
     * By default, will only use the module default unless that controller does
     * not exist; if this is the case, it falls back to the default controller
     * in the default module.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    public function getDefaultControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $controller = $this->getDefaultControllerName();
        $default    = $this->formatControllerName($controller);
        $request->setControllerName($controller)
                ->setActionName(null);

        $module              = $request->getModuleName();
        $controllerDirs      = $this->getControllerDirectory();
        $this->_curModule    = $this->getDefaultModuleName();
        $this->_curDirectory = $controllerDirs[$this->getDefaultModuleName()];
        if ($this->isValidModule($module)) {
            $moduleDir = $controllerDirs[$module];
            $fileSpec  = $moduleDir . DIRECTORY_SEPARATOR . $this->classToFilename($default);
            if (Zend_loader::isReadable($fileSpec)) {
                $this->_curModule    = $this->formatModuleName($module);
                $this->_curDirectory = $moduleDir;
            }
        }

        return $default;
    }

    /**
     * Return the value of the currently selected dispatch directory (as set by
     * {@link getController()})
     *
     * @return string
     */
    public function getDispatchDirectory()
    {
        return $this->_curDirectory;
    }

    /**
     * Determine the action name
     *
     * First attempt to retrieve from request; then from request params
     * using action key; default to default action
     *
     * Returns formatted action name
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    public function getActionMethod(Zend_Controller_Request_Abstract $request)
    {
        $action = $request->getActionName();
        if (empty($action)) {
            $action = $this->getDefaultAction();
            $request->setActionName($action);
        }

        return $this->formatActionName($action);
    }


    /**
     * Set default module name
     *
     * @param string $moduleName
     * @return Varien_Controller_Dispatcher_Standart
     */
    public function setDefaultModuleName($moduleName) {
        $this->_defaultModuleName = $moduleName;
        return $this;
    }


    /**
     * Get default module name
     *
     * @return string
     */
    public function getDefaultModuleName() {
        return $this->_defaultModuleName;
    }
}
