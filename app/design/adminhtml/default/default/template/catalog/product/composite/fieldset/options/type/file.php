<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @category    design
 * @package     default_default
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>
<?php
/* @var $this Mage_Catalog_Block_Product_View_Options_Type_File */
$_option = $this->getOption();
?>
<dt><label<?php if ($_option->getIsRequire()) echo ' class="required"' ?>><?php if ($_option->getIsRequire()) echo '<em>*</em>' ?><?php echo  $this->htmlEscape($_option->getTitle()) ?></label>
    <?php echo $this->getFormatedPrice() ?></dt>
<dd<?php if ($_option->decoratedIsLast){?> class="last"<?php }?>>
    <div class="input-box">
        <input type="file" id="option_<?php echo $_option->getId() ?>_file"  name="options_<?php echo $_option->getId() ?>_file" class="product-custom-option<?php echo $_option->getIsRequire() ? ' required-entry' : '' ?>" onchange="opConfig.reloadPrice()" />
        <?php if ($_option->getFileExtension()): ?>
        <p class="no-margin"><?php echo Mage::helper('catalog')->__('Allowed file extensions to upload')?>: <strong><?php echo $_option->getFileExtension() ?></strong></p>
        <?php endif; ?>
        <?php if ($_option->getImageSizeX() > 0): ?>
        <p class="no-margin"><?php echo Mage::helper('catalog')->__('Maximum image width')?>: <strong><?php echo $_option->getImageSizeX() ?> <?php echo Mage::helper('catalog')->__('px.')?></strong></p>
        <?php endif; ?>
        <?php if ($_option->getImageSizeY() > 0): ?>
        <p class="no-margin"><?php echo Mage::helper('catalog')->__('Maximum image height')?>: <strong><?php echo $_option->getImageSizeY() ?> <?php echo Mage::helper('catalog')->__('px.')?></strong></p>
        <?php endif; ?>
    </div>
</dd>
