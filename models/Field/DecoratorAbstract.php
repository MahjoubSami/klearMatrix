<?php
abstract class KlearMatrix_Model_Field_DecoratorAbstract
{
    const APPLY_TO_FORMS = true;
    const APPLY_TO_LISTS = false;
    const APPLY_TO_LIST_FILTERING = false;

    /**
     * Route Dispatcher desde klear/index/dispatch
     * @var KlearMatrix_Model_RouteDispatcher
     */
    protected $_mainRouter;

    /**
     * Screen|Dialog
     * @var KlearMatrix_Model_ResponseItem
     */
    protected $_item;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    protected $_pk;
    protected $_model;

    /**
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Zend_Controller_Action_HelperBroker
     */
    protected $_helper;

    /**
     * @var Zend_View
     */
    protected $_view;

    /**
     * @param Zend_Controller_Request_Http
     * @param null | Zend_Controller_Action_HelperBroker
     * @param null | Zend_View
     */
    public function __construct($request , $helper = null, $view = null)
    {
        if ($request !== null) {

            $this->setRequest($request);
        }

        if ($helper !== null) {

            $this->setHelperBroker($helper);
        }

        if ($view !== null) {

            $this->setView($view);
        }

        $this->loadDependencies();
        $this->setFrontController(Zend_Controller_Front::getInstance());
    }

    abstract function run();

    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;
    }

    public function setHelperBroker(Zend_Controller_Action_HelperBroker $helper)
    {
        $this->_helper = $helper;
    }

    public function setView(Zend_View $view)
    {
        $this->_view = $view;
    }

    public function setFrontController(Zend_Controller_Front $front)
    {
        $this->_front = $front;
    }

    public function loadDependencies()
    {
        $dataGateway = \Zend_Registry::get('data_gateway');

        $this->_mainRouter = $this->_request->getUserParam("mainRouter");
        $this->_item = $this->_mainRouter->getCurrentItem();
        $this->_pk = $this->_mainRouter->getParam("pk", false);

        if ($this->_pk) {
            $this->_model = $dataGateway->find($this->_item->getEntityClassName(), $this->_pk);
        }

        $this->_init();

        return $this;
    }
}

//EOF