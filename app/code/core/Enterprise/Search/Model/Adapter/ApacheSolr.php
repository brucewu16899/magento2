<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Solr search engine adapter that perform raw queries to Solr server based on Conduit solr client library
 * and basic solr adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
include_once 'Solr/Service.php';

class Enterprise_Search_Model_Adapter_ApacheSolr extends Enterprise_Search_Model_Adapter_Solr
{
    /**
     * Object name used to create solr document object
     *
     * @var string
     */
    protected $_clientDocObjectName = 'Apache_Solr_Document';

    /**
     * Initialize connect to Solr Client
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        try {
            $this->_connect($options);
        }
        catch (Exception $e){
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable to perform search because of search engine missed configuration.'));
        }
    }

    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param array $options
     * @return Apache_Solr_Service
     */
    protected function _connect($options = array())
    {
        $def_options = array
        (
            'hostname' => $this->getConfigData('server_hostname'),
//            'login'    => $this->getConfigData('server_username'),
//            'password' => $this->getConfigData('server_password'),
            'port'     => $this->getConfigData('server_port'),
//            'timeout'  => $this->getConfigData('server_timeout'),
            'path'  => $this->getConfigData('server_path')
        );
        $options = array_merge($def_options, $options);
        try {
            $this->_client = Mage::getSingleton('enterprise_search/client_solr', $options);
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable to connect to the search client.'));
        }
        return $this->_client;
    }

    /**
     * Simple Search interface
     *
     * @param string $query The raw query string
     * @param array $params Params can be specified like this:
     *        'offset'      - The starting offset for result documents
     *        'limit        - The maximum number of result documents to return
     *        'sort_by'     - Sort field, can be just sort field name (and asceding order will be used by default),
     *                        or can be an array of arrays like this: array('sort_field_name' => 'asc|desc')
     *                        to define sort order and sorting fields.
     *                        If sort order not asc|desc - asceding will used by default
     *        'fields'      - Fields names which are need to be retrieved from found documents
     *        'solr_params' - Key / value pairs for other query parameters (see Solr documentation),
     *                        use arrays for parameter keys used more than once (e.g. facet.field)
     *        'lang_code'   - Language code, that will be used as suffix for text fields,
     *                        by whish will be performed search request and sorting
     *
     *
     * @return array
     */
    protected function _search($query, $params = array())
    {
        $query = $this->_prepareQueryText($query);
        if (!$query) {
            return array();
        }
        $_params = $this->_defaultQueryParams;
        if (is_array($params) && !empty($params)) {
            $_params = array_intersect_key($params, $_params) + array_diff_key($_params, $params);
        }
        $offset = (int)$_params['offset'];
        $limit  = (int)$_params['limit'];

        if (!$limit) {
            $limit = 100;
        }

        $searchParams = array();

        /**
         * Now supported search only in fulltext field
         * By default in Solr  set <defaultSearchField> is "fulltext"
         * When language fields need to be used, then perform search in appropriate field
         */
        if ($this->getIsUseLanguageFields() && $params['lang_code']) {
            $query = 'fulltext_' . $params['lang_code'] . ':' . $query;
        }

        if (!is_array($_params['fields'])) {
            $_params['fields'] = array($_params['fields']);
        }

        if (!is_array($_params['solr_params'])){
            $_params['solr_params'] = array($_params['solr_params']);
        }

        /**
         * Support specifing sort by field as only string name of field
         */
        if (!empty($_params['sort_by']) && !is_array($_params['sort_by'])) {
            if ($_params['sort_by'] == 'relevance') {
                $_params['sort_by'] = 'score';
            }
            if ($_params['sort_by'] == 'name') {
                $_params['sort_by'] = 'alphaNameSort';
            }
            $_params['sort_by'] = array(array($_params['sort_by'] => 'asc'));
        }

        /**
         * Add sort fields
         */
        foreach ($_params['sort_by'] as $_key => $sort) {
            $_sort = each($sort);
            $sortField = $_sort['key'];
            $sortType = $_sort['value'];
            if ($sortField == 'relevance') {
                $sortField = 'score';
            }
            if (in_array($sortField, $this->_usedFields)) {
                if ($sortField == 'name') {
                    $sortField = 'alphaNameSort';
                }
                if (in_array($sortField, $this->_searchTextFields) && $this->getIsUseLanguageFields() && $params['lang_code']) {
                    $sortField = $sortField . '_' . $params['lang_code'];
                }
                $sortType = trim(strtolower($sortType)) == 'desc' ? 'desc' : 'asc';
                $searchParams['sort'][] = $sortField . ' ' . $sortType;
            }
        }

        /**
         * Fields to retrieve
         */
        if (!empty($_params['fields'])) {
            $searchParams['fl'] = implode(',', $_params['fields']);
        }

        /**
         * Specific Solr params
         */
        if (!empty($_params['solr_params'])) {
            foreach ($_params['solr_params'] as $name => $value) {
                $searchParams[$name] = $value;
            }
        }

        /**
         * Store filtering
         */
        if ($_params['store_id'] > 0) {
            $searchParams['fq'] = 'store_id:' . $_params['store_id'];
        }

        try {
            $this->_client->ping();
            $response = $this->_client->search($query, $offset, $limit, $searchParams);
            $data = json_decode($response->getRawResponse());
            return $this->_prepareQueryResponse($data);
        }
        catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('enterprise_search')->__('Unable perform search request.'));
        }
    }
}