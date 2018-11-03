<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  class pr_products_reviews_write_customer_agreement {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_products_reviews_write_customer_agreement_title');
      $this->description = CLICSHOPPING::getDef('modules_products_reviews_write_customer_agreement_description');

      if ( defined('MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS') ) {
        $this->sort_order = MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_SORT_ORDER;
        $this->enabled = (MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $content_width = (int)MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_CONTENT_WIDTH;

      if (isset($_GET['Products']) && isset($_GET['ReviewsWrite']) && !isset($_GET['Success'])) {

        $data = '<!-- pr_modules_products_reviews_write_customer_agreement start -->' . "\n";

        if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
          $customer_agree_privacy =  HTML::checkboxField('customer_agree_privacy', null, null, 'required aria-required="true"') . ' ' . CLICSHOPPING::getDef('text_privacy_conditions_agree');
          $text_privacy_conditions_description = CLICSHOPPING::getDef('text_privacy_conditions_description', ['store_name' => STORE_NAME, 'privacy_url' => CLICSHOPPING::link(SHOP_CODE_URL_CONFIDENTIALITY)]);
        }

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_reviews_write_customer_agreement'));

        $data .= ob_get_clean();

        $data .= '<!-- pr_modules_products_reviews_write_customer_agreement end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_SORT_ORDER',
          'configuration_value' => '600',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
        ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_STATUS',
                   'MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_CONTENT_WIDTH',
                   'MODULES_PRODUCTS_REVIEWS_WRITE_CUSTOMER_AGREEMENT_SORT_ORDER'
                  );
    }
  }
