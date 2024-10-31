<?php
/* szclasses.php is part of the ScrapeAZon plugin for WordPress
 * 
 * This file is distributed as part of the ScrapeAZon plugin for WordPress
 * and is not intended to be used apart from that package. You can download
 * the entire ScrapeAZon plugin from the WordPress plugin repository at
 * http://wordpress.org/plugins/scrapeazon/
 */

/* 
 * Copyright 2011-2020	James R. Hanback, Jr.  (email : james@jameshanback.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class szWPOptions
{
    public $szAccessKey      = '';
    public $szSecretKey      = '';
    public $szAssocId        = '';
    public $szRetrieveMethod = '';
    public $szCountryId      = '';
    public $szResponsive     = '';
    public $szTruncate       = '1000';
    public $szCountries      = array("--","AT","AU","CA","CN","DE","ES","FR","IN","IT","JP","UK","US");
    public $szCacheExpire    = 12;
    public $szClearCache     = 0;
    public $szOptionsPage    = 'scrapeaz-options';
    public $szDefer          = 0;
    public $szDisclaimerText = 'CERTAIN CONTENT THAT APPEARS ON THIS SITE COMES FROM AMAZON SERVICES LLC. THIS CONTENT IS PROVIDED \'AS IS\' AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.';

    public function szDonateLink($links,$file)
    {
        // Code based on codex.wordpress.org/Plugin_API/Filter_Reference/plugin_row_meta
        if(strpos($file, 'scrapeazon.php') !== false)
        {
            $szDonateLinks = array(
                               '<a href="http://www.timetides.com/donate" target="_blank">Donate</a>'
                               );
            $links = array_merge($links, $szDonateLinks);
        }
        return $links;
    }

    public function szLoadLocal()
    {
        load_plugin_textdomain('scrapeazon',false,basename(dirname(__FILE__)).'/lang');
    }

    public function szCleanCache()
    {
        global $wpdb;
        $szDBquery = 'SELECT option_name FROM ' . $wpdb->options . ' WHERE option_name LIKE \'_transient_timeout_szT-%\';';
        $szCleanDB = $wpdb->get_col($szDBquery);
        foreach ($szCleanDB as $szTransient) {
            $szDBKey = str_replace('_transient_timeout_','',$szTransient);
            delete_transient($szDBKey);
        }
    }
    
    public function szGetPageType()
    {
        global $post;
        $szGoodpage  = FALSE;
        $szWooBool   = FALSE;
        
        if($this->getResponsive()) {
            if(is_home() || is_front_page() || is_active_widget( false, false, 'sz_widget', true )) {
                 $szGoodpage = TRUE;
            } elseif (is_single() || is_page()) {
                if(has_shortcode($post->post_content,'scrapeazon'))
                {
                    $szGoodpage = TRUE;
                }
            }
            
            // Check for presence of WooCommerce
            if(class_exists('WooCommerce')) {
                if(is_woocommerce()) {
                   $szGoodpage = TRUE;
                }
            }
        }
        return $szGoodpage;
    }

    public function szRequireStyles()
    {
        // Load responsive stylesheet if required and if shortcode is present
        // below code does NOT work with do_shortcode and requires WP 3.6 or later

        if($this->szGetPageType())
        {
            $szStylesheet = plugins_url('szstyles.css',__FILE__);
            wp_register_style('scrape-styles',esc_url($szStylesheet));
            wp_enqueue_style('scrape-styles');
        }
        return true;
    }

    public function szOptionsLink($szLink) 
    {
        $szOptionsLink   = admin_url() . 'admin.php?page=scrapeaz-options';
        $szTestsLink     = admin_url() . 'admin.php?page=scrapeaz-tests';
        $szSettingsLink  = '<a href="' . esc_url($szOptionsLink) . '">' . __('Settings','scrapeazon') . '</a> | ';
        $szSettingsLink .= '<a href="' . esc_url($szTestsLink) . '">' . __('Test','scrapeazon') . '</a>';
        array_unshift($szLink,$szSettingsLink);
        return $szLink;
    }

    public function szOptionsScreen()
    {
        $szScreen = get_current_screen();
        return ($szScreen->id == 'scrapeazon-options') ? true : false;
    }

    public function setAccessKey($newval)
    {
        $this->szAccessKey = (strlen(trim($newval))!=20) ? '' : trim($newval);
        return sanitize_text_field($this->szAccessKey);
    }
    
    public function getAccessKey()
    {
        $this->szAccessKey = get_option('scrape-aws-access-key-id','');
        return sanitize_text_field($this->szAccessKey);
    }
    
    public function setSecretKey($newval)
    {
        $this->szSecretKey = (strlen(trim($newval))!=40) ? '' : trim($newval);
        return $this->szSecretKey;
    }
    
    public function getSecretKey()
    {
        $this->szSecretKey = get_option('scrape-aws-secret-key','');
        return sanitize_text_field($this->szSecretKey);
    }
    
    public function setAssocID($newval)
    {
        $this->szAssocID = (!preg_match('/^[A-Z0-9\_\-]*$/i', trim($newval))) ? '' : trim($newval);
        return sanitize_text_field($this->szAssocID);
    }
    
    public function getAssocID()
    {
        $this->szAssocID = get_option('scrape-amz-assoc-id','');
        return sanitize_text_field($this->szAssocID);
    }
    
    public function setCountryID($newval)
    {
        $this->szCountryID = (!in_array(trim($newval),$this->szCountries)) ? '--' : trim($newval);
        return sanitize_text_field($this->szCountryID);
    }
    
    public function getCountryID()
    {
        $this->szCountryID = get_option('scrape-getcountry','');
        return sanitize_text_field($this->szCountryID);
    }
    
    public function setResponsive($newval)
    {
        $this->szResponsive = trim($newval);
        return absint($this->szResponsive);
    }
    
    public function getResponsive()
    {
        $this->szResponsive = get_option('scrape-responsive','0');
        return absint($this->szResponsive);
    }
    
    public function setCacheExpire($newval)
    {
        $this->szCacheExpire = (! empty($newval)) ? trim($newval) : 12;
        return absint($this->szCacheExpire);
    }
    
    public function getCacheExpire()
    {
        $this->szCacheExpire = get_option('scrape-perform','12');
        return absint($this->szCacheExpire);
    }
    
    public function setClearCache($newval)
    {
        $this->szClearCache = (! empty($newval)) ? trim($newval) : 0;
        return absint($this->szClearCache);
    }
    
    public function getClearCache()
    {
        $szClear = get_option('scrape-clearcache','0');
        update_option('scrape-clearcache','0');
        return($szClear);
    }

    public function setDeferLoad($newval)
    {
        $this->szDefer = (trim($newval)=='checked') ? 1 : trim($newval);
        return absint($this->szDefer);
    }
    
    public function getDeferLoad()
    {
        $this->szDefer = get_option('scrape-defer','');
        return $this->szDefer;
    }
    
    public function setTruncate($newval)
    {
        $this->szTruncate = trim($newval);
        return absint($this->szTruncate);
    }
    
    public function getTruncate()
    {
        $this->szTruncate = get_option('scrape-truncate','1000');
        return $this->szTruncate;
    }
    
    public function getDisclaimerText()
    {
        $this->szDisclaimerText = get_option('scrape-disclaimer',$this->szDisclaimerText);
        return sanitize_text_field($this->szDisclaimerText);
    }
    
    public function setDisclaimerText($newval)
    {
        $this->szDisclaimerText = trim($newval);
        return $this->szDisclaimerText;
    }
    
       
    public function szOptionsCallback()
    {
        $sz1      = __('In order to access customer review data from Amazon.com, you must have an Amazon.com Associate ID, an Amazon Web Services (AWS) Access Key Id, and an AWS Secret Key. You can obtain an Associate ID by signing up to be an ','scrapeazon');
        $sz2      = __('Amazon.com affiliate','scrapeazon');
        $sz3      = __('. You can obtain the AWS credentials by signing up to use the ','scrapeazon');
        $sz4      = __('Product Advertising API','scrapeazon');
        $szFormat = '<p>%s<a href="https://affiliate-program.amazon.com/" target="_blank">%s</a>%s<a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html" target="_blank">%s</a>.</p>';

        printf($szFormat,$sz1,$sz2,$sz3,$sz4);
    }
    
    public function szTestCallback()
    {
        $sz1      = __('If you have correctly configured ScrapeAZon, you should see an iframe below that contains Amazon.com reviews for the Kindle short story ','scrapeazon');
        $sz2      = __('located here','scrapeazon');
        $sz3      = __(' on Amazon. The shortcode used to produce this test is ','scrapeazon');
        $sz4      = __('. If you see reviews in the iframe below, ScrapeAZon is configured correctly and should work on your site. If you see no data or if you see an error displayed below, please double-check your configuration.','scrapeazon');
        $szFormat = '<p>%s<a href="http://www.amazon.com/Dislike-Isaac-Thorne-ebook/dp/B00HPCF5VU" target="_blank">%s</a>%s<code>[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false" country="' . $this->getCountryID() . '"]</code>%s</p>';

        printf($szFormat,$sz1,$sz2,$sz3,$sz4);
    }
    
    public function szPerformCallback()
    {
        $sz1      = __('WARNING!');
        $sz2      = __('You should make a backup of your WordPress database before attempting to use the <strong>Clear Cache</strong> option. The <strong>Clear Cache</strong> option attempts to delete data directly from the WordPress database and is therefore dangerous. Use the <strong>Clear Cache</strong> option with caution.','scrapeazon');
        $szFormat = '<p><h2>%s</h2></p><p>%s</p>';

        printf($szFormat,$sz1,$sz2);
    }
    
    public function szUsageCallback()
    {
        echo '<p>' . __('A tutorial for configuring ScrapeAZon is available at the <a href="http://www.timetides.com/2015/01/09/configure-scrapeazon-wordpress" target="_blank">author\'s Web site</a>.','scrapeazon') . '</p>';
        echo '<p><b>' . __('Shortcode','scrapeazon') .'</b>: <code>[scrapeazon asin="<i>amazon.com-product-number</i>"]</code></p>';
        $sz1      = __('Insert the above shortcode into any page or post where you want Amazon.com customer reviews to appear. Replace ','scrapeazon');
        $sz2      = __(' with the product ASIN or ISBN-10 to retrieve and display the reviews for that product.','scrapeazon');
        $sz3      = __('For a more detailed and complete overview of how ScrapeAZon works, click the "Help" tab on the upper right of the ScrapeAZon settings page.','scrapeazon');
        $szFormat = '<p>%s<code><i>amazon.com-product-number</i></code>%s</p><p>%s</p>';

        printf($szFormat,$sz1,$sz2,$sz3);
    }
    
    public function szRegisterSettings() 
    {
        register_setting('scrapeazon-options','scrape-aws-access-key-id',array(&$this, 'setAccessKey'));
        register_setting('scrapeazon-options','scrape-aws-secret-key',array(&$this, 'setSecretKey'));
        register_setting('scrapeazon-options','scrape-amz-assoc-id',array(&$this, 'setAssocID'));
        register_setting('scrapeazon-options','scrape-getcountry',array(&$this, 'setCountryID'));
        register_setting('scrapeazon-options','scrape-responsive',array(&$this, 'setResponsive'));
        register_setting('scrapeazon-options','scrape-truncate',array(&$this, 'setTruncate'));
        register_setting('scrapeazon-options','scrape-disclaimer',array(&$this,'setDisclaimerText'));
        register_setting('scrapeazon-perform','scrape-perform',array(&$this, 'setCacheExpire'));
        register_setting('scrapeazon-perform','scrape-clearcache',array(&$this, 'setClearCache'));
        register_setting('scrapeazon-perform','scrape-defer',array(&$this, 'setDeferLoad'));
    }
       
    public function szAddAdminPage() 
    {
        global $wp_version;
        $szOptionsPage = add_submenu_page('options-general.php','ScrapeAZon','ScrapeAZon','manage_options','scrapeaz-options',array(&$this, 'szGetOptionsScreen'));
        $szTestingPage = add_submenu_page('scrapeaz-options','Tests','Tests','manage_options','scrapeaz-tests',array(&$this, 'szGetOptionsScreen'));
        $szCachingPage = add_submenu_page('scrapeaz-perform','Performance','Performance','manage_options','scrapeaz-perform',array(&$this, 'szGetOptionsScreen'));
        $szUsingPage   = add_submenu_page('scrapeaz-options','Usage','Usage','manage_options','scrapeaz-usages',array(&$this, 'szGetOptionsScreen'));    

        if($wp_version>=3.3) 
        {
            add_action('load-' . $szOptionsPage, array(&$this, 'szAddHelp'));
            add_action('load-' . $szTestingPage, array(&$this, 'szAddHelp'));
            add_action('load-' . $szCachingPage, array(&$this, 'szAddHelp'));
            add_action('load-' . $szUsingPage, array(&$this, 'szAddHelp'));
        }    
    }
 
    public function szGetOptionsScreen() 
    {
        switch(get_admin_page_title())
        {
            case 'ScrapeAZon':
                 $_GET['tab'] = 'scrapeazon_retrieval_section';
                 break;
            case 'Tests':
                 $_GET['tab'] = 'scrapeazon_test_section';
                 break;
            case 'Performance':
                 $_GET['tab'] = 'scrapeazon_perform_section';
                 break;
            case 'Usage':
                 $_GET['tab'] = 'scrapeazon_usage_section';
                 break;
        }
        // Settings navigation tabs
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'scrapeazon_retrieval_section';
        }
        $szRetrieveLink = admin_url() . 'admin.php?page=scrapeaz-options&tab=scrapeazon_retrieval_section';
        $szTestsLink    = admin_url() . 'admin.php?page=scrapeaz-tests&tab=scrapeazon_test_section';
        $szPerformLink  = admin_url() . 'admin.php?page=scrapeaz-perform&tab=scrapeazon_perform_section';
        $szUsageLink    = admin_url() . 'admin.php?page=scrapeaz-usages&tab=scrapeazon_usage_section';
        
        echo '<h2 class="nav-tab-wrapper"><a href="' . esc_url($szRetrieveLink) .'" class="nav-tab ';
        echo $active_tab == 'scrapeazon_retrieval_section' ? 'nav-tab-active' : '';
        echo '">ScrapeAZon</a><a href="' . esc_url($szTestsLink) .'" class="nav-tab ';
        echo $active_tab == 'scrapeazon_test_section' ? 'nav-tab-active' : '';
        echo '">Tests</a><a href="' . esc_url($szPerformLink) . '" class="nav-tab ';
        echo $active_tab == 'scrapeazon_perform_section' ? 'nav-tab-active' : '';
        echo '">Performance</a><a href="' . esc_url($szUsageLink) . '" class="nav-tab ';
        echo $active_tab == 'scrapeazon_usage_section' ? 'nav-tab-active' : '';
        echo '">Usage</a></h2>';
        
        // Settings form


        add_settings_section(
            'scrapeazon_retrieval_section',
            __('ScrapeAZon Settings','scrapeazon'),
            array(&$this, 'szOptionsCallback'),
            'scrapeaz-options'
        );
               
        add_settings_section(
            'scrapeazon_test_section',
            __('ScrapeAZon Test Frame','scrapeazon'),
            array(&$this, 'szTestCallback'),
            'scrapeaz-tests'
        );
        
        add_settings_section(
            'scrapeazon_perform_section',
            __('ScrapeAZon Performance','scrapeazon'),
            array(&$this, 'szPerformCallback'),
            'scrapeaz-perform'
        );
               
        add_settings_section(
            'scrapeazon_usage_section',
            __('ScrapeAZon Usage','scrapeazon'),
            array(&$this, 'szUsageCallback'),
            'scrapeaz-usages'
        );
        
        // Create settings fields
        $this->getOptionsForm();   
        switch($active_tab)
        {
            case 'scrapeazon_retrieval_section':
                 echo '<form method="post" action="options.php">';
                 settings_fields('scrapeazon-options');
                 do_settings_sections('scrapeaz-options');
                 echo get_submit_button();
                 echo '</form>';
                 break;
            case 'scrapeazon_test_section':
                 settings_fields('scrapeazon-tests');
                 do_settings_sections('scrapeaz-tests');
                 break;
            case 'scrapeazon_perform_section':
                 echo '<form method="post" action="options.php">';
                 settings_fields('scrapeazon-perform');
                 do_settings_sections('scrapeaz-perform');
                 echo get_submit_button();
                 echo '</form>';
                 break;
            case 'scrapeazon_usage_section':
                 settings_fields('scrapeazon-usages');
                 do_settings_sections('scrapeaz-usages');
                 break;
        }

    }
    
    public function szAWSKeyIDField($args)
    {
        $szField  = '<input type="password" id="scrape-aws-access-key-id" name="scrape-aws-access-key-id" value="';
        $szField .= $this->getAccessKey();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-aws-access-key-id"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSSecretField($args)
    {
        $szField  = '<input type="password" id="scrape-aws-secret-key" name="scrape-aws-secret-key" value="';
        $szField .= $this->getSecretKey();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-aws-secret-key"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSAssocField($args)
    {
        $szField  = '<input type="text" id="scrape-amz-assoc-id" name="scrape-amz-assoc-id" value="';
        $szField .= $this->getAssocID();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-amz-assoc-id"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szDisclaimerField($args)
    {
        $szField  = '<textarea id="scrape-disclaimer" name="scrape-disclaimer" rows="5" cols="30">';
        $szField .= $this->getDisclaimerText();
        $szField .= '</textarea><br />';
        $szField .= '<label for="scrape-disclaimer"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szCountryField($args)
    {
	    $szField = '<select id="scrape-getcountry" name="scrape-getcountry">';
	    foreach($this->szCountries as $szDDitem) 
	    {
		    $szFieldSelected = (($this->getCountryID())==$szDDitem) ? ' selected="selected"' : '';
		    $szField .= '<option value="' .
		                sanitize_text_field($szDDitem) .
		                '"' .
		                sanitize_text_field($szFieldSelected) .
		                '>' .
		                sanitize_text_field($szDDitem) .
		                '</option>';
	    }
	    $szField .= '</select><br />';
        $szField .= '<label for="scrape-getcountry"> '  . sanitize_text_field($args[0]) . '</label>';
	    echo $szField;
    }
    
    public function szTruncateField($args)
    {
        $szField  = '<input type="text" id="scrape-trucate" name="scrape-truncate" value="';
        $szField .= $this->getTruncate();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-truncate"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szCacheExpireField($args)
    {
	    $szField = '<select id="scrape-perform" name="scrape-perform">';
	    for($x=1;$x<24;$x++)
	    {
	        $szFieldSelected = ($this->getCacheExpire()==$x) ? ' selected="selected"' : '';
	        $szField .= '<option value="' .
	                    absint($x) .
	                    '"' .
	                    sanitize_text_field($szFieldSelected) .
	                    '>' .
	                    absint($x) .
	                    '</option>';
	    }
	    $szField .= '</select> Hours<br />';
        $szField .= '<label for="scrape-perform"> '  . sanitize_text_field($args[0]) . '</label>';
	    echo $szField;
    }
    
    public function szResponsiveField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-responsive" id="scrape-responsive" value="1" ' .
                     checked(1, $this->getResponsive(), false) .
                     ' /><br />';
        $szField .= '<label for="scrape-responsive"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSTestField()
    {
    
        echo do_shortcode('[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false country="'. $this->getCountryID() .'"]');
    }
    
    public function szClearCacheField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-clearcache" id="scrape-clearcache" value="1" /><br />';
        $szField .= '<label for="scrape-clearcache"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szDeferField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-defer" id="scrape-defer" value="1" ' .
        checked(1, $this->getDeferLoad(), false) .
        $this->getDeferLoad() .
        ' /><br />';
        $szField .= '<label for="scrape-defer"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function getOptionsForm()
    {
        add_settings_field(
            'scrape-aws-access-key-id',
            __('AWS Access Key ID','scrapeazon'),
            array(&$this, 'szAWSKeyIDField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your 20-character AWS Access Key. This must be your root AWS Access Key.','scrapeazon')
            )
        );

        add_settings_field(
            'scrape-amz-secret-key',
            __('AWS Secret Key','scrapeazon'),
            array(&$this, 'szAWSSecretField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your 40-character AWS Secret Key. This must be your root AWS Secret Key.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-aws-assoc-id',
            __('Amazon Associate ID','scrapeazon'),
            array(&$this, 'szAWSAssocField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your Amazon Advertising Associate ID.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-disclaimer',
            __('Alternate Disclaimer','scrapeazon'),
            array(&$this, 'szDisclaimerField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('If you prefer to use your own disclaimer language, enter it here. Remember that Amazon requires text similar to this to appear somewhere on your site to maintain compliance with the Amazon Advertising API Terms of Service.','scrapeazon')
            )
        );
        
        
        add_settings_field(
            'scrape-getcountry',
            __('Amazon Country ID','scrapeazon'),
            array(&$this, 'szCountryField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select the country code for the Amazon International API from which you want to pull reviews.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-truncate',
            __('Truncate Reviews At','scrapeazon'),
            array(&$this, 'szTruncateField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Number of characters at which to truncate reviews (0 returns entire review).','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-responsive',
            __('Use Responsive Style','scrapeazon'),
            array(&$this, 'szResponsiveField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select this checkbox to enable ScrapeAZon styles for sites with responsive design.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-aws-test-field',
            __('Test Frame','scrapeazon'),
            array(&$this, 'szAWSTestField'),
            'scrapeaz-tests',
            'scrapeazon_test_section',
            array(
                __('ScrapeAZon Test Frame.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-perform',
            __('Cache Expires In','scrapeazon'),
            array(&$this, 'szCacheExpireField'),
            'scrapeaz-perform',
            'scrapeazon_perform_section',
            array(
                __('The number of hours that should pass before cached Amazon API calls expire. Cannot be more than 23 hours. Default is 12.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrapeazon-defer',
            __('Defer Until Footer','scrapeazon'),
            array(&$this, 'szDeferField'),
            'scrapeaz-perform',
            'scrapeazon_perform_section',
            array(
                __('Loads the ScrapeAZon iframe data asynchronously for better site performance. This option ONLY works if you do not use the "url" shortcode parameter. See the Help menu for more information.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-clear-cache-field',
            __('Clear Cache','scrapeazon'),
            array(&$this, 'szClearCacheField'),
            'scrapeaz-perform',
            'scrapeazon_perform_section',
            array(
                __('Clears ScrapeAZon transient data.','scrapeazon')
            )
        );
    }
    
    public function szAddHelp($szContextHelp)
    {
        $szOverview     = '<p>' .
                          __('The ScrapeAZon plugin retrieves Amazon.com customer reviews for products you choose and displays them in pages or posts on your WordPress blog by way of a WordPress shortcode.','scrapeazon') .
                          '</p> <p>' .
                          __('You must be a participant in both the Amazon.com Affiliate Program and the Amazon.com Product Advertising API in order to use this plugin. Links to Amazon.com forms to join those programs are available on the ScrapeAZon Settings page.','scrapeazon') .
                          '</p>';
        $szSettingsUse  = '<p>' .
                          __('The following ScrapeAZon Settings fields are ','scrapeazon') .
                          '<strong>' .
                          __('required','scrapeazon') .
                          '</strong>:<ul><li><strong>AWS Access Key</strong>: ' .
                          __('A 20-character key assigned to you by the AWS Product Advertising API.','scrapeazon') .
                          '</li><li><strong>AWS Secret Key</strong>: ' .
                          __('A 40-character secret key assigned to you by the Amazon Product Advertising API.','scrapeazon') .
                          '</li><li><strong>Amazon Associate ID</strong>: ' .
                          __('The short string of characters that identifies your Amazon associate account.','scrapeazon') .
                          '</li></ul></p><p>' .
                          __('The following ScrapeAZon Settings are optional','scrapeazon') .
                          ':<ul><li><strong>Amazon Country ID</strong>: ' .
                          __('If you select a country here, you will globally enable that country for all your ScrapeAZon shortcodes. If you leave it blank, ScrapeAZon shortcodes will default to reviews from Amazon US unless the ','scrapeazon') .
                          '<code>country</code>' .
                          __(' parameter is specified in the shortcode.','scrapeazon') .
                          '</li><li><strong>Truncate Reviews At</strong>: ' .
                          __('Specifying 0 here will always return the entirety of reviews. If you specify a positive value other than 0, the text of each review will be truncated to that number of characters. The default truncate value is 1000 characters. This global setting can be overridden at the shortcode level by configuring the shortcode ','scrapeazon') .
                          '<code>truncate</code>' .
                          __(' parameter. ','scrapeazon') .
                          '</li><li><strong>Use Responsive Style</strong>: ' .
                          __('Selecting this checkbox loads a default ScrapeAZon style sheet that will attempt to scale output for sites that have a responsive design. If you specify the ','scrapeazon') .
                          '<code>width</code> ' .
                          __('and ','scrapeazon') .
                          '<code>height</code> ' .
                          __('parameters in a shortcode, the containing element will default to that width and height.','scrapeazon') .
                          '</li></ul></p>';
        $szShortcodeUse = '<p>' .
                          __('Type the shortcode ','scrapeazon') .
                          '<code>[scrapeazon asin="<i>amazon-asin-number</i>"]</code>,' .
                          __(' where ','scrapeazon') .
                          '<i>amazon-asin-number</i> ' .
                          __('is the ASIN or ISBN-10 of the product reviews you want to retrieve. The shortcode must be issued in text format in your page or post, not Visual format. Otherwise, the quotation marks inside the shortcode might be rendered incorrectly.','scrapeazon') .
                          '</p><p>' .
                          __('You can also issue the ScrapeAZon shortcode with one of the following identifers instead of using an ASIN','scrapeazon') .
                          ':<ul><li><code>isbn</code>: ' .
                          __('Retrieves reviews by using an International Standard Book Number (ISBN) value','scrapeazon') .
                          '</li><li><code>upc</code>: ' .
                          __('Retrieves reviews by using a Universal Product Code (UPC) value (not valid in CA locale)','scrapeazon') .
                          '</li><li><code>sku</code>: ' .
                          __('Retrieves reviews by using a stock keeping unit (SKU) value','scrapeazon') .
                          '</li><li><code>ean</code>: ' .
                          __('Retrieves reviews by using a European Article Number (EAN) value','scrapeazon') .
                          '</li></ul></p><p>' .
                          __('You can also issue the ScrapeAZon shortcode with the following additional parameters','scrapeazon') .
                          ':<ul><li><code>width</code>: ' .
                          __('Specifies the width of the reviews iframe, or of the containing element if the responsive option is enabled. Append a percent (%) symbol to this value if you are specifying your width in percentages rather than pixels. Otherwise, specify digits only.','scrapeazon') .
                          '</li><li><code>height</code>: ' .
                          __('Specifies the height of the reviews iframe, or of the containing element if the responsive option is enabled. Append a percent (%) symbol to this value if you are specifying your height in percentages rather than pixels. Otherwise, specify digits only.','scrapeazon') .
                          '</li><li><code>border</code>: ' .
                          __('When set to ','scrapeazon') .
                          '<code>false</code>, ' .
                          __('disables the border that some browsers automatically add to iframes.','scrapeazon') .
                          '</li><li><code>country</code>: ' .
                          __('Overrides the global country setting on the Settings page. Use the two-character country code for the Amazon International site from which you want to obtain reviews.','scrapeazon') .
                          '</li><li><code>noblanks</code>: ' .
                          __('When set to ','scrapeazon') .
                          '<code>true</code> ' .
                          __('prevents ScrapeAZon from displaying an iframe for products that have no reviews. By default, ScrapeAZon displays an iframe that contains Amazon\'s "Be the first to review this item" page.','scrapeazon') .
                          '</li><li><code>url</code>: ' .
                          __('When set to ','scrapeazon') .
                          '<code>true</code>, ' .
                          __('returns ONLY the iFrame source URL, not the iFrame itself. This can be useful if you want more advanced control over the iframe element attributes because you can include the shortcode within the SRC attribute of a manually coded iframe tag. Default value is','scrapeazon') .
                          '<code>false</code>' . 
                          '</li></ul></p>';
        $szTestsUse     = '<p>' .
                          __('After you have saved your ScrapeAZon Settings by clicking the ','scrapeazon') .
                          '<strong>' .
                          __('Save Changes','scrapeazon') .
                          '</strong> ' .
                          __('button, you can click the ','scrapeazon') .
                          '<strong>' .
                          __('Tests','scrapeazon') .
                          '</strong> ' .
                          __('tab to view some sample reviews frames based on your settings.','scrapeazon') .
                          '</p><p>' . 
                          __('If you do not see sample Amazon output on this tab, your ScrapeAZon settings might be incorrect.','scrapeazon') .
                          '</p>';
        $szPerfUse      = '<p>' .
                          __('By default, ScrapeAZon caches Amazon API calls for 12 hours to enhance site performance. ','scrapeazon') .
                          __('You can adjust the amount of time ScrapeAZon caches this data by adjusting the','scrapeazon') .
                          '<strong>' .
                          __('Cache Expires In','scrapeazon') .
                          '</strong> ' .
                          __('value to the number of hours you want the cached data to persist.','scrapeazon') .
                          '</p><p>' . 
                          __('You can also choose to clear the existing cached data from the WordPress database. However, you should always back up your WordPress database before attempting to delete data in bulk.','scrapeazon') .
                          '</p><p>' .
                          __('Select the ','scrapeazon') .
                          '<strong>' .
                          __('Defer Until Footer','scrapeazon') .
                          '</strong>' .
                          __(' field to ensure that the entire main page of your site loads before ScrapeAZon attempts to load the reviews iframe. This option only works for the iframe. If you use a caching plugin, such as W3 Total Cache, you might need to adjust your settings for this option to work properly.','scrapeazon') .
                          '</p><p>' .
                          __('Please be aware that if you are using a caching plugin, such as W3 Total Cache, with object caching enabled, the Clear Cache option will not do anything. You will need to clear the object cache by using the caching plugin\'s clear cache feature.','scrapeazon') .
                          '</p>';
    
        $szScreen   = get_current_screen();           
        $szScreen->add_help_tab(array(
            'id'      => 'szOverviewTab',
            'title'   => __('Overview','scrapeazon'),
            'content' => $szOverview,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szSettingsUseTab',
            'title'   => __('Settings','scrapeazon'),
            'content' => $szSettingsUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szShortcodeUseTab',
            'title'   => __('Shortcode','scrapeazon'),
            'content' => $szShortcodeUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szTestsUseTab',
            'title'   => __('Tests Tab','scrapeazon'),
            'content' => $szTestsUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szPerfUseTab',
            'title'   => __('Performance Tab','scrapeazon'),
            'content' => $szPerfUse,
        ));
        return $szContextHelp;
    }
    
    public function szShowNPNotices()
    {
        if($this->getClearCache()=='1') 
        {
            add_settings_error( 'scrapeazon-notices', 'scrape-cache-cleared', __('Cache cleared', 'scrapeazon'), 'updated' );
            $this->szCleanCache();
        }
        settings_errors('scrapeazon-notices');
    }
    
    private function szActivate()
    {
        // Primarily applies for upgrades to 2.1.9 and later
        $szUser       = wp_get_current_user();
        $szMeta_type  = 'user';
        $szUser_id    = 0;
        $szMeta_value = '';
        $szDelete_all = true;

        delete_option('scrape-getmethod');
        delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_FileGetEnabled', $szMeta_value, $szDelete_all );
        delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_CurlEnabled', $szMeta_value, $szDelete_all );
        delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_CurlDisabled', $szMeta_value, $szDelete_all );
    }
}

class szWidget extends WP_Widget {

    public function __construct() 
    {
	    parent::__construct(
		    'sz_widget', 
		    __('Amazon Reviews','scrapeazon'),
		    array( 'description' => __( 'Display Amazon.com reviews for a product you specify.','scrapeazon'), )
	    );
    }
    
    public function widget($args, $instance)
    {  
		$title    = apply_filters( 'widget_title', $instance['title'] );
		$szBArray = array('true','false');

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		if( isset ($instance[ 'asin' ]) )
		{		    
            $szAtts = array(
                           'asin'       => ($instance['itype']=='asin') ? $instance['asin'] : '',
                           'upc'        => ($instance['itype']=='upc' ) ? $instance['asin'] : '',
                           'isbn'       => ($instance['itype']=='isbn') ? $instance['asin'] : '',
                           'ean'        => ($instance['itype']=='ean' ) ? $instance['asin'] : '',
                           'sku'        => ($instance['itype']=='sku' ) ? $instance['asin'] : '',
                           'border'     => ((in_array($instance['border'],$szBArray)) && isset ($instance[ 'border' ]) ) ? $instance['border'] : 'false',
                           'width'      => (($instance['width']!=0) && isset ($instance[ 'width' ]) ) ? $instance['width'] : '',
                           'height'     => (($instance['height']!=0) && isset ($instance[ 'height' ]) ) ? $instance['height'] : '',
                           'country'    => '--',
                           'truncate'   => ($instance['truncate']!='1000' ) ? $instance['truncate'] : '',
                           'url'        => 'false',
                           'noblanks'   => 'false',
                           'iswidget'   => 'true'
                       );
            $szShcd = new szShortcode;
            echo $szShcd->szParseShortcode($szAtts);
            unset($szShcd);
		}
		echo $args['after_widget'];
    }
    
    public function form($instance)
    {
		if ( isset( $instance[ 'title' ] ) ) 
		{
			$title = $instance[ 'title' ];
		}
		else 
		{
			$title = __( 'Amazon Reviews', 'scrapeazon' );
		}
		if ( isset( $instance[ 'itype'] ) )
		{
		    $itype = $instance['itype'];
		}
		else
		{
		    $itype = 'asin';
		}
		if ( isset( $instance[ 'asin' ] ) ) 
		{
		    $asin = $instance[ 'asin' ];
		}
		else
		{
		    $asin = '';
		}
		if ( isset( $instance[ 'width' ] ) ) 
		{
		    $width = $instance[ 'width' ];
		}
		else
		{
		    $width = '';
		}
		if ( isset( $instance[ 'height' ] ) ) 
		{
		    $height = $instance[ 'height' ];
		}
		else
		{
		    $height = '';
		}
		if ( isset( $instance[ 'border' ] ) ) 
		{
		    $border = $instance[ 'border' ];
		}
		else
		{
		    $border = '';
		}
		if ( isset( $instance[ 'truncate' ] ) ) 
		{
		    $truncate = $instance[ 'truncate' ];
		}
		else
		{
		    $truncate = '1000';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','scrapeazon'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
 		<label for="<?php echo $this->get_field_id( 'itype' ); ?>"><?php _e('ID Type:','scrapeazon'); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id( 'itype' ); ?>" name="<?php echo $this->get_field_name( 'itype' ); ?>">
        <option value="asin" <?php echo (esc_attr($itype)=='asin') ? 'selected' : ''; ?>>ASIN</option>
        <option value="ean" <?php echo (esc_attr($itype)=='ean') ? 'selected' : ''; ?>>EAN</option>
        <option value="isbn" <?php echo (esc_attr($itype)=='isbn') ? 'selected' : ''; ?>>ISBN</option>
        <option value="sku" <?php echo (esc_attr($itype)=='sku') ? 'selected' : ''; ?>>SKU</option>
        <option value="upc" <?php echo (esc_attr($itype)=='upc') ? 'selected' : ''; ?>>UPC (not valid in CA locale)</option>
        </select>
 		<label for="<?php echo $this->get_field_id( 'asin' ); ?>"><?php _e('ID:','scrapeazon'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'asin' ); ?>" name="<?php echo $this->get_field_name( 'asin' ); ?>" type="text" value="<?php echo esc_attr( $asin ); ?>">
 		<label for="<?php echo $this->get_field_id( 'truncate' ); ?>"><?php _e('Truncate Reviews At:','scrapeazon'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'truncate' ); ?>" name="<?php echo $this->get_field_name( 'truncate' ); ?>" type="text" value="<?php echo esc_attr( $truncate ); ?>">
 		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','scrapeazon'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>">
 		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height:','scrapeazon' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>">
 		<label for="<?php echo $this->get_field_id( 'border' ); ?>"><?php _e('Border (true/false):','scrapeazon' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'border' ); ?>" name="<?php echo $this->get_field_name( 'border' ); ?>" type="text" value="<?php echo esc_attr( $border ); ?>">
		</p>
		<?php 
    }
    
    public function update($new_instance, $old_instance)
    {
		$instance = array();
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['itype']    = ( ! empty( $new_instance['itype'] ) ) ? strip_tags( $new_instance['itype'] ) : '';
		$instance['asin']     = ( ! empty( $new_instance['asin'] ) ) ? strip_tags( $new_instance['asin'] ) : '';
		$instance['truncate'] = ( ! empty( $new_instance['truncate'] ) ) ? strip_tags( $new_instance['truncate'] ) : '';
		$instance['width']    = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['height']   = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
		$instance['border']   = ( ! empty( $new_instance['border'] ) ) ? strip_tags( $new_instance['border'] ) : '';
		return $instance;
    }
    
}

class szShortcode
{   
    public $szIFrameWidget        = '';
    public $szElementID           = '';
    
    public function szGetDomain($szCountryId)
    {
        switch ($szCountryId) {
        case "AT" :
             return '.de';
             break;
        case "AU" :
             return '.com.au';
             break;
        case "CA" :
             return '.ca';
             break;
        case "CN" :
             return '.cn';
             break;
        case "DE" :
             return '.de';
             break;  
        case "ES" :
             return '.es';
             break;
        case "FR" :
             return '.fr';
             break;
        case "IN" :
             return '.in';
             break;  
        case "IT" :
             return '.it';
             break;
        case "JP" :
             return '.co.jp';
             break;
        case "UK" :
             return '.co.uk';
             break;
        case "US" :
             return '.com';
             break;
        case "--" :
             return '.com';
             break;  
        }  
    }

    public function szIsSSL()
    {
        $szSSL = (isset($_SERVER['HTTPS'])) || (is_ssl()) ? 'https://' : 'http://';
        return $szSSL;
    }

    public function szGetSignature($szHost,$szPath,$szQuery,$szSecret)
    {
         /* 
         The code in this function is adapted from a function found online at
         http://randomdrake.com/2009/07/27/amazon-aws-api-rest-authentication-for-php-5/
         */
     
         $pQuery = $szQuery;
         ksort($pQuery);
         foreach ($pQuery as $parameter => $value) {
             if($value!='None') 
             {
                 $parameter     = str_replace("%7E", "~", rawurlencode($parameter));
                 $value         = str_replace("%7E", "~", rawurlencode($value));
                 $query_array[] = $parameter . '=' . $value;
             }
         }
         $newSZQuery = implode('&', $query_array);
         $szSigString = "GET\n{$szHost}\n{$szPath}\n{$newSZQuery}";
         $szSignature = urlencode(base64_encode(hash_hmac('sha256', $szSigString, $szSecret, true)));
         $szQueryStr = "?{$newSZQuery}&Signature={$szSignature}";
         
         return $szQueryStr;
    }
    
    public function szGetIDType($szASIN,$szUPC,$szISBN,$szEAN,$szSKU)
    {      
        $szItemType = 'ASIN';
        
        if(! empty($szEAN))  { $szItemType = 'EAN'; }
        if(! empty($szUPC))  { $szItemType = 'UPC'; }
        if(! empty($szISBN)) { $szItemType = 'ISBN'; }
        if(! empty($szSKU))  { $szItemType = 'SKU'; }
        
        return $szItemType;
    }

    public function szAmazonURL($szASIN,$szUPC,$szISBN,$szEAN,$szSKU,$szCountry,$szTruncate,$szSummary)
    {
        $szSets   = new szWPOptions();
        $szSecret = $szSets->getSecretKey();
        
        $szSSLR   = $this->szIsSSL();
        
        $szItemID = $this->szGetIDType($szASIN,$szUPC,$szISBN,$szEAN,$szSKU);
        
        $szUCCountry = ($szCountry!='--') ? strtoupper($szCountry) : strtoupper($szSets->getCountryID());
        $szDomain = (in_array($szUCCountry,$szSets->szCountries)) ? $this->szGetDomain($szUCCountry) : '.com';
        
        $szTruncate = ($szTruncate=='1000') ? absint($szSets->getTruncate()) : absint($szTruncate);
        $szSummary  = (strtoupper($szSummary)!='FALSE') ? 'true' : 'false';
        
        $szHost = 'webservices.amazon' . $szDomain;
        
        $szPath = '/onca/xml';
        
        $szItemNum = sanitize_text_field($szASIN);
        $szItemNum = (! empty($szUPC)) ? sanitize_text_field($szUPC) : sanitize_text_field($szASIN);
        $szItemNum = (! empty($szISBN)) ? sanitize_text_field($szISBN) : sanitize_text_field($szASIN);
        $szItemNum = (! empty($szEAN)) ? sanitize_text_field($szEAN) : sanitize_text_field($szASIN);
        $szItemNum = (! empty($szSKU)) ? sanitize_text_field($szSKU) : sanitize_text_field($szASIN);
        
        $szQuery = array(
                       'AssociateTag'          => $szSets->getAssocID(),
                       'Availability'          => 'Available',
                       'AWSAccessKeyId'        => $szSets->getAccessKey(),
                       'Condition'             => 'All',
                       'IncludeReviewsSummary' => $szSummary,
                       'TruncateReviewsAt'     => $szTruncate,
                       'ItemId'                => $szItemNum,
                       'IdType'                => $szItemID,
                       'SearchIndex'           => (($szItemID != 'ASIN') ? 'All' : 'None'),
                       'MerchantId'            => 'All',
                       'Operation'             => 'ItemLookup',
                       'ResponseGroup'         => 'Reviews',
                       'Service'               => 'AWSECommerceService',
                       'Timestamp'             => gmdate("Y-m-d\TH:i:s\Z"),
                       'Version'               => '2013-08-01'
                   );

         $szAWSURI = $szSSLR . $szHost . $szPath . $this->szGetSignature($szHost,$szPath,$szQuery,$szSecret);
         
         unset($szSets);
         return $szAWSURI;   
    }

    public function szCallAmazonAPI($szURL)
    {
        $szRetries = 0;
        $szSCCode  = "500";
        
        while(($szRetries<5)&&($szSCCode!='200'))
        {
            usleep(500000*pow($szRetries,2));
            $szResponse  = wp_remote_get($szURL);
            $szSCCode    = wp_remote_retrieve_response_code($szResponse);
            $szSCCodeMsg = wp_remote_retrieve_response_message($szResponse);
            $szRetries   = $szRetries + 1;
        }
        
        if($szSCCode==200) 
        {
           $szXML = wp_remote_retrieve_body($szResponse);
        } else {
           $szXML = "<?xml version=\"1.0\" ?><ItemLookupResponse><Items><Request><Errors><Error><Code>{$szSCCode} {$szSCCodeMsg}</Code><Message>ScrapeAZon could not connect to Amazon or was otherwise unable to retrieve data from Amazon. Please check your Internet connectivity, your ScrapeAZon settings, your country code, and your shortcode configuration.</Message></Error></Errors></Request></Items></ItemLookupResponse>";
        }
        return $szXML;
    }
    
    public function szRetrieveFrameURL($szResults)
    {
        $szIFrameURL='';
        if(! empty($szResults->Items->Item->CustomerReviews->HasReviews)) 
        {
            $szIFrameURL = str_replace('http://',$this->szIsSSL(),$szResults->Items->Item->CustomerReviews->IFrameURL);
        }
        else
        {
            if($szResults->Items->Request->Errors->Error->Message)
            { 
                echo '<div class="scrape-error">';
                echo '<h2>' . $szResults->Items->Request->Errors->Error->Code . '</h2><p>' . $szResults->Items->Request->Errors->Error->Message . '</p>';
                echo '</div>';
            }
        }
        return $szIFrameURL;
    }
    
    public function szShowDisclaimer($szWidth,$szRespBool,$szDisclaimerText)
    {
        // Make sure disclaimer is the same width as the iframe/responsive container
        $szDWidth = ($this->szMatchDigits($szWidth)) ? ' style="width:' . $szWidth . $this->szPctOrPixel($szWidth) . ';" ' : '';
        $szDisclaimer = '<div id="scrapezon-disclaimer" class="scrape-api"' . $szDWidth . '>' .
                        $szDisclaimerText .
                        '</div>';
        return $szDisclaimer;
    }
    
    public function szPctOrPixel($szParam)
    {
       if (preg_match('/^\d*(?:\%|px)$/',$szParam)) 
       {
           $szMatches = '';
       } else {
           $szMatches = 'px';
       }
       return $szMatches;
    }
    
    public function szMatchDigits($szParam)
    {
       $szMatches = ((preg_match('/^\d*$/',$szParam) || preg_match('/^\d*(?:\%|px)$/',$szParam))&&(! is_null($szParam))&&(! empty($szParam))) ? true : false;
       return $szMatches;
    }

    public function szShowIFrame($szNoBlanks,$szBorder,$szWidth,$szHeight,$szFrameURL,$szHasReviews)
    {
        $szOutput  = '';
        if((false===(strtolower($szHasReviews)=='true'))&&(true===(strtolower($szNoBlanks)=='true')))
        {
            $szOutput = '';
        } else {
            $szSets           = new szWPOptions();
            $szRespBool       = absint($szSets->getResponsive());
            $szDisclaimerText = $szSets->getDisclaimerText(); 
            if($szRespBool) 
            {
                $szOutput .= '<div id="scrapeazon-wrapper" class="scrapeazon-responsive"';
                if (($this->szMatchDigits($szWidth)) || ($this->szMatchDigits($szHeight)))
                { 
                    $szOutput .= ' style="';
                    $szOutput .= ($this->szMatchDigits($szWidth)) ? 'width:' . $szWidth . $this->szPctOrPixel($szWidth) . ';' : '';
                    $szOutput .= ($this->szMatchDigits($szHeight)) ? 'height:' . $szHeight . $this->szPctOrPixel($szHeight) . ';' : '';
                    $szOutput .= '"';
                }
                 $szOutput .= '>';
            }
        
            $szOutput .= '<iframe id="scrapeazon-iframe" class="scrapeazon-reviews" src="' . 
                         esc_url($szFrameURL) .
                         '" style="';
            $szOutput .= (strtolower($szBorder)=='true') ? 'border:medium single rgb(0,0,0);' : 'border:none;';
            $szOutput .= ((!$szRespBool)&&($this->szMatchDigits($szWidth))) ? 'width:' . $szWidth . $this->szPctOrPixel($szWidth) . ';' : '';
            $szOutput .= ((!$szRespBool)&&($this->szMatchDigits($szHeight))) ? 'height:' . $szHeight . $this->szPctOrPixel($szHeight) . ';' : '';
            $szOutput .= '"></iframe>';
            $szOutput .= ($szRespBool) ? '</div>' : '';
            $szOutput .= $this->szShowDisclaimer($szWidth,$szRespBool,$szDisclaimerText);
        
            unset($szSets);
        }
        return $szOutput;
    }
    
    public function szShowURL($szFrameURL)
    {
        return esc_url($szFrameURL);
    }
    
    public function szTransientID($szSCAtts)
    {
           $szScreen          = (is_admin()) ? get_current_screen()->id : '';
           $szTransient       = "szT-";
           $szTransientValues = '';
           if ($szScreen != 'admin_page_scrapeaz-tests')
           {
               $szTransientValues  = $this->szIsSSL() . implode('',$szSCAtts);
               $szTransient       .= wp_hash($szTransientValues);
           } else {
               $szTransient       .= 'testpanel';
           }
           return $szTransient;
    }
    
    public function szDeferReviews()
    {
        echo '<script type="text/javascript">' .
             '$jhgrQuery = jQuery.noConflict();' .
             ' $jhgrQuery(document).ready(function() {' .
             '    $jhgrQuery(\'#deferscrape' . $this->szElementID . '\').append(\'' . str_replace('\'','\\\'',$this->szIFrameWidget) . '\');' . 
             ' });' .
             '</script>';
    }
    
    public function szQuicktag()
    {
        if(wp_script_is('quicktags'))
        {
        ?>
            <script type="text/javascript">
            QTags.addButton('eg_scrapeazon','SAz','[scrapeazon asin="" width="" height="" truncate="1000" summary="true" border="false"]','','scrapeazon','ScrapeAZon Shortcode');
            </script>
        <?php
        }
    }
    
    public function szRemoveExpired($szTransientID)
    {
        $szBool = false;
        $szTimeoutID = '_transient_timeout_' . $szTransientID;
        $szTransientTimeout = get_option($szTimeoutID);
        
        if ($szTransientTimeout<time())
           $szBool = true;
        return $szBool;
    }

    public function sz_sunset_notice() {
        $szdismissdate = new DateTime(get_option('scrapeazon-dismiss'));
        $sztoday = new DateTime(date('Y-m-d H:i:s'));
        if($sztoday>=$szdismissdate->modify("+72 hours")) {
    ?>
    <div id="szsunset" class="notice scrapeazon-notice notice-warning is-dismissible">
        <p><?php _e( 'IMPORTANT! On March 9, 2020, the Amazon Product Advertising API will <a href="https://www.timetides.com/2020/01/22/amazon-product-advertising-api-update-affects-scrapeazon-plugin/" target="_blank">cease to support</a> the retrieval of customer product reviews. Therefore, the <b>ScrapeAZon plugin</b> will cease to function. You currently have the ScrapeAZon plugin installed and activated on your site.<br/><br/>Before March 9, 2020, you should deactivate ScrapeAZon and seek an alternate reviews plugin.<br/><br/>You can dismiss this message for 3 days.', 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php
        } 
    }
    
    public function sz_eol_notice() {
        $szdismissdate = new DateTime(get_option('scrapeazon-dismiss'));
        $sztoday = new DateTime(date('Y-m-d H:i:s'));
        if($sztoday>=$szdismissdate->modify("+720 hours")) {
    ?>
    <div id="szeol" class="notice scrapeazon-notice notice-error is-dismissible">
        <p><?php _e( 'IMPORTANT! You have the <b>ScrapeAZon plugin</b> installed and activated. On March 9, 2020, this plugin ceased to function because of <a href="https://www.timetides.com/2020/01/22/amazon-product-advertising-api-update-affects-scrapeazon-plugin/" target="_blank">changes to the Amazon Product Advertising API</a> that rendered it obsolete.<br/><br/>You should deactivate ScrapeAZon and seek an alternate reviews plugin.<br/><br/>You can dismiss this message for 30 days.', 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php
        }
    }
    
    public function szNoShortcode()
    {
        $szOutput = "";
        return $szOutput;
        unset ($szSets);
    }

    public function sz_enqueue_scripts($hook) {
        wp_enqueue_script(
            'scrapeazon-js',
            plugin_dir_url( __FILE__ ) . 'js/scrapeazon.js'
        );
    }
    
    public function sz_display_dismissible_admin_notice() {
        $szdismissdate = date('Y-m-d H:i:s');
        update_option( 'scrapeazon-dismiss', $szdismissdate );
        wp_die();  
    }

    public function create_custom_option() {
        $szdismissdate = '2020-01-01 12:00:00';
        update_option( 'scrapeazon-dismiss', $szdismissdate );
    }

    public function szParseShortcode($szAtts)
    {
        // When does our cache expire?
        $szSets            = new szWPOptions();
        $szErrors          = __('<p>Some ScrapeAZon settings have not been configured.</p>','scrapeazon');
        $szOutput          = '';

        if((! $szSets->getAccessKey())||(! $szSets->getSecretKey())||(! $szSets->getAssocID()))
        {
             return $szErrors;
        } else {
            $szTransientExpire = $szSets->getCacheExpire();
        
            $szSCAtts = shortcode_atts( array(
                     'asin'       => '',
                     'upc'        => '',
                     'isbn'       => '',
                     'ean'        => '',
                     'sku'        => '',
                     'border'     => 'false',
                     'width'      => '',
                     'height'     => '',
                     'country'    => '--',
                     'url'        => 'false',
                     'noblanks'   => 'false',
                     'iswidget'   => 'false',
                     'truncate'   => '1000',
                     'summary'    => 'true'
	               ), $szAtts);
	           
	        $szTransientID = $this->szTransientID($szSCAtts);
            $this->szElementID   = '-' . $szTransientID;
	               
            if ((false === ($szOutput = get_transient($szTransientID))) || $this->szRemoveExpired($szTransientID) ||($szTransientID=='szT-testpanel'))
            {
                $szURL        = $this->szAmazonURL($szSCAtts['asin'],$szSCAtts['upc'],$szSCAtts['isbn'],$szSCAtts['ean'],$szSCAtts['sku'],$szSCAtts['country'],$szSCAtts['truncate'],$szSCAtts['summary']);
                $szXML        = $this->szCallAmazonAPI($szURL);
                $szResults    = simplexml_load_string($szXML);
                $szHasReviews = (! empty($szResults->Items->Item->CustomerReviews->HasReviews)) ? $szResults->Items->Item->CustomerReviews->HasReviews : false;

                $szFrameURL   = $this->szRetrieveFrameURL($szResults);
                      
                if(true === ($szSCAtts['url']==strtolower('true'))) 
                {
                    set_transient ($szTransientID, $this->szShowURL($szFrameURL), $szTransientExpire * 3600);
                    $szOutput = get_transient($szTransientID);
                } else {
                    if($szTransientID=='szT-testpanel')
                    {
                        $szOutput = $this->szShowIFrame($szSCAtts['noblanks'],$szSCAtts['border'],$szSCAtts['width'],$szSCAtts['height'],$szFrameURL,$szHasReviews);
                    } else {
                        set_transient ($szTransientID, $this->szShowIFrame($szSCAtts['noblanks'],$szSCAtts['border'],$szSCAtts['width'],$szSCAtts['height'],$szFrameURL,$szHasReviews), $szTransientExpire * 3600 );
                        $szOutput = get_transient($szTransientID);
                    }
                }
            } else {
                $szOutput = get_transient($szTransientID);
            }
        }

        if(($szSets->getDeferLoad()=='1')&&($szTransientID!='szT-testpanel')&&($szSCAtts['url']!=strtolower('true')))
        {
            $this->szIFrameWidget .= $szOutput;
            $this->szIFrameWidget = str_replace(array("\n", "\t", "\r"), '', $this->szIFrameWidget);
            add_action('wp_footer', array(&$this,'szDeferReviews'),100);
            return '<div id="deferscrape' . $this->szElementID . '"></div>';
        } else {
           return $szOutput;
        }
        unset($szSets);
    }
}

?>