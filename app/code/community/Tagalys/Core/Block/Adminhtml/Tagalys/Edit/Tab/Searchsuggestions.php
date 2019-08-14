<?php

class Tagalys_Core_Block_Adminhtml_Tagalys_Edit_Tab_Searchsuggestions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareForm() {
        $this->_helper = Mage::helper('tagalys_core');

        $form = Mage::getModel('varien/data_form', array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/tagalys', array('_current'  => true)),
            'method'  => 'post'
        ));

        $form->setHtmlIdPrefix('admin_tagalys_core_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('tagalys_searchsuggestions_fieldset', array('legend' => $this->__('Search Suggestions')));

        $fieldset->addField('enable_searchsuggestions', 'select', array(
            'name' => 'enable_searchsuggestions',
            'label' => 'Enable',
            'title' => 'Enable',
            'options' => array(
                '0' => $this->__('No'),
                '1' => $this->__('Yes'),
            ),
            'required' => true,
            'style' => 'width:100%',
            'value' => Mage::getModel('tagalys_core/config')->getTagalysConfig("module:search_suggestions:enabled")
        ));

        $fieldset->addField('submit', 'submit', array(
            'name' => 'tagalys_submit_action',
            'value' => 'Save Search Suggestions Settings',
            'class'=> "tagalys-btn",
            'tabindex' => 1
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Search Suggestions');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->__('Search Suggestions');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden() {
        return false;
    }

}