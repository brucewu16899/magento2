<?php
/** 
 * Adminhtml newsletter template edit form
 * 
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
     /** 
     * Constructor
     * 
     * Initialize form
     */
    public function __construct() 
    {
        parent::__construct();
        
    }
    
   
    /**
     * Prepare form for render
     */
    public function renderPrepare($template)
    {
        $form = new Varien_Data_Form();
        
        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if (isset($post['template_id'])) {
                unset($post['template_id']);
            } 
            
            if (isset($post['template_type'])) { 
                unset($post['template_type']);
            }
            
            $template->addData($post);
        }
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Template General')));
        
        $fieldset->addField('code', 'text', array(
            'name'=>'code',
            'label' => __('Template Code'),
            'title' => __('Template Code'),
            'class' => 'required-entry validate-alphanum',
            'required' => true,
             'value' => $template->getTemplateCode()   
        ));
        
        $fieldset->addField('subject', 'text', array(
            'name'=>'subject',
            'label' => __('Template Subject'),
            'title' => __('Template Subject'),
            'value' => $template->getTemplateSubject()            
        ));
        
        $fieldset->addField('sender_name', 'text', array(
            'name'=>'sender_name',
            'label' => __('Sender Name'),
            'title' => __('Sender Name'),
            'value' => $template->getTemplateSenderName()
        ));
        
        $fieldset->addField('sender_email', 'text', array(
            'name'=>'sender_email',
            'label' => __('Sender Email'),
            'title' => __('Sender Email'),
            'class' => 'validate-email',
            'value' => $template->getTemplateSenderEmail()   
        ));       
        
        $txtType = constant(Mage::getConfig()->getModelClassName('newsletter/template') . '::TYPE_TEXT');
        
        $fieldset->addField('text', 'editor', array(
            'name'=>'text',
            'wysiwyg' => ($template->getTemplateType() != $txtType),
            'label' => __('Template Content'),
            'title' => __('Template Content'),
            'cols' => 20,
            'rows' => 15,
            'theme' => 'advanced',
            'value' => $template->getTemplateText()
        ));
        
        if ($template->getId()) { 
            // If edit add id
            $form->addField('id', 'hidden', 
                array(
                    'name'  => 'id',
                    'value' => $template->getId()
                )
            );
        }
        
        if($values = Mage::getSingleton('adminhtml/session')->getData('newsletter_template_form_data', true)) {
        	$form->setValues($values);
        }
        
        $this->setForm($form);
        
        return $this;
    }
}
