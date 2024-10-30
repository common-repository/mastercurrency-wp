<?php
/*
Plugin Name: Master Currency WP
Plugin URI: http://www.wibergsweb.se/plugins/mastercurrencywp
Description: Master Currency WP uses the European Central Bank rates to convert between currencies in form(s), list or single conversion(s) in a page/post.
Version: 1.1.61
Author: Wibergs Web
Author URI: http://www.wibergsweb.se/
Text Domain: mastercurrency-wp
Domain Path: /lang
License: GPLv2
*/
defined( 'ABSPATH' ) or die( 'No access allowed!' );

if( !class_exists('mastercurrencywp') ) {
    class mastercurrencywp
    {
    private $xml_object;
    private $currency_description = null;
                    
    private $errormessage = null;
    private $last_result = null;

    private $default_amount = 100; 
    private $last_amount =100; //Should be same as $default_amount set here

    private $last_currencies = array('from' => 'EUR', 'to' => 'USD'); //Default values
    private $currency_amount = 1; //Amount to convert with when listing currencies
    private $result_decimals = 4;
    private $result_sanitize = true;   //Sanitize to float number from user when entering in currency form
    

    /*
    *  Constructor
    *
    *  This function will construct all the neccessary actions, filters and functions for the mastercurrencywp plugin to work
    *
    *
    *  @param	N/A
    *  @return	N/A
    */	
    public function __construct() 
    {                        
        add_action( 'init', array( $this, 'loadjslanguage' ) );
    }
    

    /*
     * loadjs
     * 
     * This function load javascript and (if there are any) translations
     *  
     *  @param	N/A
     *  @return	N/A
     *                 
     */    
    public function loadjslanguage() 
    {                       
        wp_enqueue_script( 'jquery' );
            
        wp_enqueue_script(
            'mastercurrencyjs',
            plugins_url( '/js/wibergsweb.js' , __FILE__, array('jquery') )
        );               

        //Initate ajax-functionality
        add_action( 'wp_ajax_convertcurrency', array ( $this, 'convert_currency') );
        add_action( 'wp_ajax_nopriv_convertcurrency', array ( $this, 'convert_currency') );       
        
        //Load (if there are any) translations
        $loaded_translation = load_plugin_textdomain( 'mastercurrency-wp', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
        
        if ($this->currency_description === null) 
        {
            $this->set_currencydescriptions(); //Use default if not set                       
        }    


   }      

   
    /*
     * set_resultdecimals
     * 
     * This function sets nr of decimal when converting from one currency to another
     * 
     *  @param	$currency_amount
     *  @return	N/A
     *                 
     */
    public function set_resultdecimals( $nr_decimals = null) 
    {
        if ($nr_decimals === null) {
            $this->errormessage = 'Nr of decimals must be set - set_resultdecimals()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;
         }    
        if (!is_numeric($nr_decimals)) {
            $this->errormessage = 'Valued specificed must be numeric- set_resultdecimals()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;
         }                            
         $this->result_decimals = $nr_decimals;
    }
    
    
    /*
     * do_sanitize
     * 
     * This function make tells plugin to remove sanitization of user input (in currency form(s))
     * 
     *  @param	N/A
     *  @return	N/A
     *                 
     */
    public function remove_sanitization() 
    {
        $this->result_sanitize = false;
    }    


    /*
     * set_currencyamount
     * 
     * This function sets the amount that is used for converting in a currency-list
     * Shortcode-equal to: [mcwp_currencylist amount="{amount}"]
     * 
     *  @param	$currency_amount                Amount to set (integer)        
     *  @return	N/A
     *                 
     */
    public function set_currencyamount($currency_amount = null) 
    {
    if ($currency_amount === null) 
    {
        $this->errormessage = 'Currency amount not set - set_currencyamount()';
        add_action( 'admin_notices', array($this,'error_notice' ) ); 
        return;
    }                 
    $this->currency_amount = $currency_amount;
    }


    /*
     * set_currencydescriptions
     * 
     * This function is used for setting description(s) for currency/currencies
     * 
     *  @param $currency_descriptions       user defined currency and its description
     *  @param $include_defaults                include defaults even if user default(s) are specified
     *  @return N/A
     *                 
     */                
    public function set_currencydescriptions($currency_descriptions = null, $include_defaults = false) 
    {
        $curr_desc = array();

        //Default
        if ($currency_descriptions === null || $include_defaults === true) 
        {
            $curr_desc['EUR'] = __('Euro', 'mastercurrency-wp');
            $curr_desc['USD'] = __('US dollar', 'mastercurrency-wp');
            $curr_desc['JPY'] = __('Japanese yen', 'mastercurrency-wp');
            $curr_desc['BGN'] = __('Bulgarian lev', 'mastercurrency-wp');
            $curr_desc['CZK'] = __('Czech koruna', 'mastercurrency-wp');
            $curr_desc['DKK'] = __('Danish krone', 'mastercurrency-wp');
            $curr_desc['GBP'] = __('Pound sterling', 'mastercurrency-wp');
            $curr_desc['HUF'] = __('Hungarian forint', 'mastercurrency-wp');
            $curr_desc['PLN'] = __('Polish zloty', 'mastercurrency-wp');
            $curr_desc['RON'] = __('New Romanian leu', 'mastercurrency-wp');
            $curr_desc['SEK'] = __('Swedish krona', 'mastercurrency-wp');
            $curr_desc['CHF'] = __('Swiss franc', 'mastercurrency-wp');
            $curr_desc['NOK'] = __('Norwegian krone', 'mastercurrency-wp');
            $curr_desc['HRK'] = __('Croatian kuna', 'mastercurrency-wp');
            $curr_desc['RUB'] = __('Russian rouble', 'mastercurrency-wp');
            $curr_desc['TRY'] = __('New Turkish lira', 'mastercurrency-wp');
            $curr_desc['AUD'] = __('Australian dollar', 'mastercurrency-wp');
            $curr_desc['BRL'] = __('Brazilian real', 'mastercurrency-wp');
            $curr_desc['CAD'] = __('Canadian dollar', 'mastercurrency-wp');
            $curr_desc['CNY'] = __('Chinese yuan renminbi', 'mastercurrency-wp');
            $curr_desc['HKD'] = __('Hong Kong dollar', 'mastercurrency-wp');
            $curr_desc['IDR'] = __('Indonesian rupiah', 'mastercurrency-wp');
            $curr_desc['ILS'] = __('Israeli shekel', 'mastercurrency-wp');
            $curr_desc['INR'] = __('Indian rupee', 'mastercurrency-wp');
            $curr_desc['KRW'] = __('South Korean won', 'mastercurrency-wp');
            $curr_desc['MXN'] = __('Mexican peso', 'mastercurrency-wp');
            $curr_desc['MYR'] = __('Malaysian ringgit', 'mastercurrency-wp');
            $curr_desc['NZD'] = __('New Zealand dollar', 'mastercurrency-wp');
            $curr_desc['PHP'] = __('Philippine peso', 'mastercurrency-wp');
            $curr_desc['SGD'] = __('Singapore dollar', 'mastercurrency-wp');
            $curr_desc['THB'] = __('Thai baht', 'mastercurrency-wp');
            $curr_desc['ZAR'] = __('South African rand', 'mastercurrency-wp');
        }

        if ($currency_descriptions !== null) 
        {
            //User defined currencies
            if (!is_array($currency_descriptions)) 
            {
                $this->errormessage = 'User defined currency descriptions must be array - set_currencydescriptions()';
                add_action( 'admin_notices', array($this,'error_notice' ) ); 
                return;
            }
            foreach($currency_descriptions as $key=>$value) 
            {
                $curr_desc[$key] = $value;                                        
            }
        }

        if (empty($curr_desc)) 
        {
            $this->errormessage = 'No descriptions set - set_currencydescriptions()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;
        }
        $this->currency_description = $curr_desc;        
    }


    /*
     *  error_notice
     * 
     *  This function is used for handling administration notices when user has done something wrong when initiating this object
     *  Shortcode-equal to: No shortcode equavilent
     * 
     *  @param N/A
     *  @return N/A
     *                 
     */                 
    public function error_notice() 
    {
        $message = $this->errormessage;
        echo"<div class=\"error\"><strong>MasterCurrencyWP Error:</strong><p>$message</p></div>"; 
    }


    /*
     *  set_selectedcurrencies
     * 
     *  This function is used to set from and to - selected currencies when showing the currencyconverter form
     *  Shortcode-equal to: No shortcode equavilent
     * 
     *  @param $currencies                      array where form and to are keys of two elements
     *  @return N/A
     *                 
     */                      
    public function set_selectedcurrencies($currencies = null) 
    {
        if ($currencies === null) 
        {
            $this->errormessage = 'No default currencies set - set_selectedcurrencies()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;
        }
        if (!is_array($currencies)) 
        {
            $this->errormessage = 'Currencies must be an array existing of two default currencies, default from and default to - set_selectedcurrencies()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;
        }
        //default currencies in format currency1, currency2 (USD, EUR)
        $this->last_currencies = $currencies;
    }


    /*
     *  set_amount
     * 
     *  This function is used to set amount that is used as default in the currency converter form
     * 
     *  @param $amout                       array where form and to are keys of two elements
     *  @return N/A
     *                 
     */          
    public function set_amount($amount) 
    {
        if (!is_numeric($amount)) 
        {
            $this->errormessage = 'Amount must be numeric - set_amount()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;                                
        }
        $this->default_amount = $amount;
        $this->last_amount = $amount;
    }


    /*
     *  init
     * 
     *  This function initiates the actual shortcodes, creating of forms, currency-list etc after user defined settings are set.
     *  Shortcode-equal to: No shortcode equavilent
     * 
     *  @param N/A
     *  @return N/A
     *                 
     */        
    public function init() 
    {       
        $this->load_rates();

        //If form is posted
        //Then do calculation of selected currencies and amount
        if (isset($_GET['doconvert'])) 
        {
            if (isset($_POST['from_ecb']) && isset($_POST['to_ecb']) && isset($_POST['amount_ecb'])) 
            {
                    //Calculate conversion rates and put result into object'
                    $from_ecb = $_POST['from_ecb'];
                    $to_ecb = $_POST['to_ecb'];
                    $amount = $_POST['amount_ecb']; 
                    
                    //Set nr of decimals (given in shortcode)
                    if (isset ($_POST['result_decimals'])) {
                        $rd = (int)$_POST['result_decimals'];
                        $this->set_resultdecimals( $rd );
                    }

                    //Set sanitize(or not) (given in shortcode)
                    if (isset ($_POST['result_sanitize'])) {
                        $san = $_POST['result_sanitize'];
                        if ( $san == 'no' ) {
                            $this->remove_sanitization();
                        }
                    }                    
                    
                    $this->last_result = $this->convertamount($amount, $from_ecb, $to_ecb);
                    $this->last_currencies = array('from' => $from_ecb, 'to' => $to_ecb);
                    $this->last_amount = $amount;
                }
            }

            //Add shortcodes
            add_shortcode( 'mcwp_updateddate', array($this, 'get_lastupdated_date') );
            add_shortcode( 'mcwp_currencyconverterform', array($this, 'currency_form') );     
            add_shortcode( 'mcwp_currencylist', array($this, 'currency_list') );
            add_shortcode( 'mcwp_currencyconvert', array ( $this, 'convert_currency') );
    }

    
    /*
     *  get_lastupdated_date
     * 
     *  Load xml-file. If success, then save to file
     * If failure, fetch information from that saved file instead of external source
     * This makes the conversion "usable" even if external site/source is not active/"down"  
     * 
     *  @param N/A
     *  @return string      date
     *                 
     */ 
    private function load_rates() 
    {
        $xml_file = "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";       
        $plugin_lastfecthed_file = WP_PLUGIN_DIR . '/mastercurrency-wp/rates/ecb_lastfetched.xml';        
        
        libxml_use_internal_errors( true ); //error handling enabled for xml
        $xml = @simplexml_load_file( $xml_file ); //Must suppress this, else user will get a warning
        if ($xml === false) {
            //Failure when trying to access external source, fetch xml from last local fetched xml file
            $xml = simplexml_load_file( $plugin_lastfecthed_file );
        }
        else {
            //Success, save this xml to file 
            $xml->asXml( $plugin_lastfecthed_file );            
        }        
        libxml_clear_errors(); //Clear memory buffer
        libxml_use_internal_errors( false ); //error handling disabled for xml
        
        //Store loaded xml into this object
         $this->xml_object = $xml;         
    }
    
         
    /*
     *  get_lastupdated_date
     * 
     *  This function returns the last updated date from ECB
     * 
     *  @param N/A
     *  @return string      date
     *                 
     */      
    public function get_lastupdated_date() 
    {
        $date = $this->xml_object->Cube->Cube;
        return $date['time'];                       
    }


    /*
     *   get_currencies
     * 
     *  This function returns all currencies included from ECB
     *  It also adds EUR as an currency because ECB calculates its exchange-rate based on EUR
     * 
     *  @param N/A
     *  @return array           currencies
     *                 
     */      
    public  function get_currencies() 
    {
        $items = $this->xml_object->Cube->Cube->Cube;

        $curr = array();
        $curr[] = 'EUR';
        foreach($items  as $item) 
        { 
                $curr[] = $item['currency'];
        } 
        return $curr;
    }


    /*
     *   get_rates
     * 
     *  This function returns all rates included from ECB
     *  It also adds 1 as an rate so EUR would be 1 so calculation would be correct
     * 
     *  @param N/A
     *  @return array           rates
     *                 
     */    
    public function get_rates() 
    {
        $items = $this->xml_object->Cube->Cube->Cube;

        $rates = array();
        $rates[] = 1; //EUR
        foreach($items  as $item)
        { 
                $rates[] = $item['rate'];
        } 
        return $rates;
    }

    
    /*
     *   currency_pairs_string
     * 
     *  This function returns currencies-array from 
     *  a string containing currency-pairs (eg  "EUR-USD, GBP-EUR")
     * 
     *  @param string $currency_pairs_string                  currencies in a string like example above
     *  @return array                                                           currencies. If empty returns null
     *                 
     */        
    private function currency_pairs_string( $currency_pairs_string = null)
    {
        $currencies = array();
        //Translate $args['currencies'] to array
        //first curr-second corr, first-curr-second, first-curr-second curr etc
        $explode_shortcodecurrencies = explode(',', $currency_pairs_string );
        foreach($explode_shortcodecurrencies as $es) 
        {
            $currs = explode('-', $es); //EUR-USD would be array('EUR', 'USD')
            if ((int)count($currs) === 2) { //Only include when array has two elements                                    
                //Remove blank spaces left and right of each element in $currs-array
                $currs[0] = trim($currs[0]);
                $currs[1] = trim($currs[1]);                                        
                $currencies[] = $currs;
            }
        }
        
        if ( empty( $currencies ) ) {
            return null;
        }
        
        return $currencies;        
    }
    

    /*
     *   sort_secondcurrency
     * 
     *  This function is a helper-function used for ordering array(s). Sorting by second currency
     * 
     *  @return                        order array by second item (1) in multidimensional array
     *                 
     */    
    private function sort_secondcurrency($a, $b) {        
        if ($a[1] == $b[1]) return 0;
        return ($a[1] < $b[1]) ? -1 : 1;
    }

    
    /*
     *   sort_result
     * 
     *  This function is a helper-function used for ordering array(s). Sorting by key result (from a multidimensional array)
     * 
     *  @return                        order array by second item (1) in multidimensional array
     *                 
     */        
    private function sort_result($a, $b) {        
        if ($a['result'] == $b['result']) return 0;
        return ($a['result'] < $b['result']) ? -1 : 1;
    }

    
    /*
     *   currency_list
     * 
     *  This function creates an actual currency list in html format that is easy to style with css
     * 
     *  @param  string $attr             shortcode attributes
     *  @return   string                      html-content
     *                 
     */    
    public function currency_list( $attrs ) 
    {
        $defaults = array(
            'html_id' => null,
            'amount' => $this->currency_amount,
            'currencies' => 'EUR-USD,USD-EUR,EUR-SEK,SEK-EUR,EUR-GBP,GBP-EUR',
            'result_decimals' => $this->result_decimals,
            'separator' => null,
            'order_by' => null //first_currency = alphabetically order by first currency, second_curency = alphabetically order by second currency
        );

        //Extract values from shortcode and if not set use defaults above
        $args = wp_parse_args( $attrs, $defaults );
        extract( $args ); //from_title = $args{'from_title'] etc
        
        //Nr of decimals given in shortcode
        if (isset( $args['result_decimals'] )) {
            $this->result_decimals = (int)$args['result_decimals'];        
        }
        
        //Separator between each item
        $use_separator = '';
        if (isset($args['separator'])) {
            $sep = $args['separator'];
            if ($sep !== null) {
                if ($sep === '<br>' || $sep === '<br />' || $sep === '<br/>') {
                    $use_separator = $sep;
                }
                else {
                    $use_separator = '<span class="sep">' . $sep . '</span>';
                }
            }
        }

        //Order by        
        if (isset($args['order_by'])) {
            $ob = $args['order_by'];
            if ($ob !== null) {
               $ob = strtolower( trim($ob) );
               if ( $ob === 'first_currency') {
                   //Make currencies into an array, 
                   //sort them alhabetically and then implode
                   //them back into a string
                   $curr_arr = explode ( ',', $args['currencies'] );
                   sort ( $curr_arr );
                   $args['currencies'] = implode(',', $curr_arr );
               }
               else if ( $ob === 'second_currency') 
               {
                   //Create an array of currency pairs:
                   $curr_pair = $this->currency_pairs_string ( $args['currencies'] );
                   
                   //Sort by second currency
                   usort( $curr_pair, array( $this, 'sort_secondcurrency') );
                    
                    $final_currarr = array();                    
                    foreach($curr_pair as $cp) {
                        $final_currarr[] = $cp[0] . '-' . $cp[1];
                    }
                    $args['currencies'] = implode(',', $final_currarr );                   
               }
               else if ( $ob === 'result')
               {                   
                //Create an array with the converted amount included  
                $amount = $args['amount'];        
                $convert_array = $this->currency_pairs_string( $args['currencies'] );

                $curr_withresults = array();  
                foreach($convert_array as $item) 
                { 
                $from_ecb = $item[0];
                $to_ecb = $item[1];
                $result = $this->convertamount($amount, $from_ecb, $to_ecb);
                $curr_withresults[] = array(
                                                                'from' => $from_ecb,
                                                                'to' => $to_ecb,
                                                                'result' => $result
                                                                    );
                }
                usort($curr_withresults, array($this, 'sort_result')); 
                
                //Glue together the sorted array
                $curr_sortedresult = array();
                foreach($curr_withresults as $cr) {
                    $curr_sortedresult[] = $cr['from'] . '-' . $cr['to'];
                }
                $args['currencies'] = implode(',', $curr_sortedresult );

            }
        }
        
        }
        
         //Which currencies to convert based on amount $args['currencies_amount']
        $amount = $args['amount'];        
        $convert_array = $this->currency_pairs_string( $args['currencies'] );
        
        $html = "";        
        $cnt = count($convert_array)-1;
        $nr = 0;
        $pyj_class = 'odd';
        
        //If id is set for this currency list, create div wrapper with that id
        $end_wrapper = false;
        if ( $args['html_id'] !== null) {
            $html = '<div id="' . $args['html_id'] . '">';
            $end_wrapper = true;
        }
        
        foreach($convert_array as $item) 
        { 
            $from_ecb = $item[0];
            $to_ecb = $item[1];
            $result = $this->convertamount($amount, $from_ecb, $to_ecb);
            $html .= "<span class=\"mcwp-convert-ecb {$pyj_class}\">";
            $html .= "<span class=\"mcwp-convertfrom\">{$amount} $from_ecb</span>";                                
            $html .= "<span class=\"mcwp-convertequal\">=</span>";                                
            $html .= "<span class=\"mcwp-convertto\">{$result} $to_ecb</span>";
            $html .= "</span>";
                        
            //Pyjamas rows (odd/even)
            if ($pyj_class ==='odd') {
                $pyj_class = 'even';
            }
            else {
                $pyj_class = 'odd';
            }
            //If separator given, do not add it on the last item
            if ( $nr < $cnt) {
                $html .= $use_separator;
            }
            

            
            
            $nr++;
        }
        
        //This is set to true if html id is set for this currency list
        if ($end_wrapper === true) {
            $html .= '</div>';
        }

         return $html;
    }

    
    /*
     *   convertamount
     * 
     *  This function converts an amount between two given currencies
     * 
     *  @param int $amount              amount  to convert
     *  @param string $from              from currency (eg. EUR, CAD etc)
     *  @param string $to                  to currency (eg. CAD, EUR etc)
     *  @return int                              result of conversion
     *                 
     */          
    public function convertamount( $amount = null, $from= null , $to = null, $return_rates = false) {  
        if ( $amount === null) {
            $this->errormessage = 'no given amount - convertamount()';
            add_action( 'admin_notices', array($this,'error_notice' ) ); 
            return;           
        }
        
        //Set sanitize(or not) (given in shortcode)
        if (isset ($_POST['result_sanitize'])) {
            $san = $_POST['result_sanitize'];
            if ( $san == 'no' ) {
                $this->remove_sanitization();
            }
        }            

        //Do sanization of amount
        if ( $this->result_sanitize === true ) {
            $amount_ecb = str_replace(',', '.', $amount);
            $amount_ecb2 = str_replace(' ', '', $amount_ecb);
            $amount = filter_var( $amount_ecb2, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ); 
        }
        else {
            $amount = $amount;
        }
        
        $from_ecb = strtoupper($from);
        $to_ecb = strtoupper($to);
        
        $rate_to = 1;
        $rate_from = 1;

        //Loop through curreny/rates and set result when found 
        //to-currency in form
        $items =$this->xml_object->Cube->Cube->Cube;
        foreach($items  as $item) 
        {                                            
             if ($item['currency'] == $from_ecb) 
             {
                $rate_from = (float)$item['rate'];        
             }
             if ($item['currency'] == $to_ecb) 
             {
                $rate_to = (float)$item['rate'];                               
             }                                             
        }
        
        //Calculate actual rate based on from and to currencies
        $res = (float)$amount * ($rate_to / $rate_from);
        $result = number_format( $res, $this->result_decimals );
        
        //Include rates in return array
        if ( $return_rates === true ) {
            return array('result' => $result, 'rate_from' => $rate_from, 'rate_to' => $rate_to);
        }
        
        //Return result
        return $result;

    }
    
    
    /*
     *   convert_currency(single pair conversion)
     * 
     *  This function converts currency between two given currencies
     * 
     *  @param string $attr         shortcode attributes
     *  @return string                  html-content
     *                 
     */         
    public function convert_currency( $attrs ) {
        if ( isset($_POST['from']) && isset($_POST['to'])) {
            $this->last_currencies['from'] = $_POST['from'];
            $this->last_currencies['to'] = $_POST['to'];
        }        
        if ( isset ($_POST['amount'])) {
            $amount = $_POST['amount'];
        }
        else {
            $amount = 1;
        }
        if ( isset($_POST['result_decimals']) )  {
            $rd = (int)$_POST['result_decimals'];
            $this->result_decimals = $rd;
        }
        
       $defaults = array(
            'html_id' => null,
            'amount' => $amount,
            'from' => $this->last_currencies['from'],
            'to' => $this->last_currencies['to'],
            'result_decimals' => $this->result_decimals,
            'display_amount' => 'yes',
            'display_type' => 1,
            'result_sanitize' => 'yes'
        );

        //Extract values from shortcode and if not set use defaults above
        $args = wp_parse_args( $attrs, $defaults );
        extract( $args );     

       //Remove sanizitation user input
        if ($args['result_sanitize'] === 'no') {
            $this->remove_sanitization();
        }
        

        $from_ecb = $args['from'];
        $to_ecb = $args['to'];        
        $display_amount = strtolower( $args['display_amount'] );
        $display_type = (int)$args['display_type'];
        if (isset ( $args['result_decimals'] )) {
            $this->result_decimals = (int)$args['result_decimals'];        
        }
        
        
        $result = $this->convertamount($amount, $from_ecb, $to_ecb);        
        
        //Use ajax to return html?
        if (isset( $_POST['use_ajax'])) {
                $result_values = array();
                //Do sanization of amount
                if ( $this->result_sanitize === true ) {
                    $amount_ecb = str_replace(',', '.', $amount);
                    $amount_ecb2 = str_replace(' ', '', $amount_ecb);
                    $amount = filter_var( $amount_ecb2, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ); 
                }
                
                $result_values['from'] = $amount . ' ' . $from_ecb . ' = ';
                $result_values['to'] = $result . ' ' .$to_ecb;
                echo json_encode( $result_values );
                wp_die();
        }        

        //If html id is set in shortcode, set that id for this span
        if ( $args['html_id'] === null) {
            $apply_html_id = '';
        }
        else {
            $apply_html_id = 'id="' . $args['html_id'] . '" ';
        }
        $html = "<span {$apply_html_id}class=\"mcwp-singleconvert\">";
        
        //Display type 1 = "x from currency = to currency" (eg. 555EUR = 425.8404GBP)        
        if ($display_type <1 || $display_type>2) {
            $display_type = 1;
        }
        
        if ($display_type === 1) {
            if ( $display_amount === 'yes') {
                $html .= '<span class="amount">' . $args['amount'] . '<span class="from_ecb">' . $from_ecb . '</span></span><span class="eq">=</span>';
            }
            $html .= '<span class="result">';            
            $html .= $result;
            if ( $display_amount === 'yes') {
                $html .= '<span class="toecb">' . $to_ecb . '</span>';
            }
            $html .= '</span>'; //end result
            
            $html .= '</span>'; //end single convert
        }

        //Display type 2 = "x from currency (to currency)" (eg. 555EUR (425.8404GBP) )        
        if ($display_type === 2) {
            if ( $display_amount === 'yes') {
                $html .= '<span class="amount">' . $args['amount'] . '<span class="from_ecb">' . $from_ecb . '</span></span>';
            }
            $html .= '<span class="result">';
            $html .= '(' . $result;
            if ( $display_amount === 'yes') {
                $html .= '<span class="toecb">' . $to_ecb . '</span>';
            }
            $html .= ')</span>'; //end result
            
            $html .= '</span>'; //end single convert

        }          

        return $html;
    }


    /*
     *   currency_form
     * 
     *  This function creates an actual currency form in html format that is easy to style with css
     * 
     *  @param string $attr          shortcode attributes
     *  @return string                    html-content
     *                 
     */     
    public function currency_form( $attrs ) 
    {                        
        $defaults = array(
            'html_id' => null,
            'input_type' => 'text', //text or number(html5)
            'default_amount' => $this->default_amount,
            'amount_title' => __('Amount', 'mastercurrency-wp'),
            'from_title' => __('From', 'mastercurrency-wp'),
            'to_title' => __('To', 'mastercurrency-wp'),
            'result_title' => __('Result', 'mastercurrency-wp'),
            'calculatebutton_title' =>  __('Calculate', 'mastercurrency-wp'),
            'show_currencydescription' => 'yes',
            'currencies' => null,
            'default_fromcurrency' => $this->last_currencies['from'],
            'default_tocurrency' => $this->last_currencies['to'],
            'use_ajax' => 'no',
            'result_decimals' => $this->result_decimals,
            'result_sanitize' => 'yes',
            'order_by' => null
         );

        //Extract values from shortcode and if not set use defaults above
        $args = wp_parse_args( $attrs, $defaults );
        extract( $args ); //from_title = $args{'from_title'] etc   
        
        //Set default amount (if not post submitted). If several shortcodes 
        //are used, then the last set default amount is used here
        if (empty ( $_POST ) && isset( $args['default_amount']) ) 
        {
            $this->set_amount( $args['default_amount'] );
        }
        
        //Remove sanizitation user input
        if ($args['result_sanitize'] === 'no') 
        {
            $this->remove_sanitization();
        }
        
        //Nr of decimals given in shortcode
        if (isset( $args['result_decimals'] )) 
        {
            $this->result_decimals = (int)$args['result_decimals'];        
        }
        
        //If currencies set, use user-defined currencies                
        if ( $args['currencies'] !== null) 
        {
            $currencies = explode(',',  $args['currencies'] );
            foreach( $currencies as &$c) {
                $c = trim($c);
            }
        }
        
        //Currencies not set. If user-defined currencies return null ,get all currencies from external source
        if ( $currencies === null ) 
        {
            $currencies = $this->get_currencies(); //get all currencies
        }
         
        //To view values that users has submitted / typed in before
        $amount = $this->default_amount;

        //Set default values (only when not posted values)
        //When posted, use last posted currency
        if (!isset($_POST['from_ecb'])) 
        {
            $from_curr = strtoupper( $args['default_fromcurrency'] );
        }
        else 
        {
            $from_curr = $this->last_currencies['from'];
        }        
        if (!isset($_POST['to_ecb'])) 
        {        
            $to_curr = strtoupper( $args['default_tocurrency'] );
        }
        else {
            $to_curr = $this->last_currencies['to'];
        }        
        
        //Make sure from currency and to currency exists in the currencies-array
        //(the currencies array are either user-defined or fetched from ecb)
        //If they don't use default (or posted) values
        if ( in_array( $from_curr , $currencies) === false ) 
        {
            $from_curr = $this->last_currencies['from'];
        }
        if ( in_array( $to_curr , $currencies) === false ) 
        {
            $to_curr = $this->last_currencies['to'];
        }        
        
        //Form should use ajax to return html(values) ? (value is put in data-attribute in form)
        $use_ajax = $args['use_ajax'];
        if ($use_ajax == 'yes') {            
            $this->last_result = $this->convertamount($amount, $from_curr, $to_curr); //Default calculation
            $use_ajax = 1;
        }
        else {
            $use_ajax = 0;
        }        
        
        //Create the actual form

        //If html id is set in shortcode, set that id for this form
        if ( $args['html_id'] === null) {
            $apply_html_id = '';
        }
        else {
            $apply_html_id = 'id="' . $args['html_id'] . '" ';
        }
        $html  = "<form {$apply_html_id}data-useajax=\"{$use_ajax}\" class=\"mcwp-currencyconverterform\"  name=\"frmMasttercurrencyWPConvert[]\" action=\"?doconvert\" ";
        $html .= "method=\"post\">";

        $html .= "<div class=\"mcwp-selectamount\">";
        $html .= "{$args['amount_title']}:";
        
        //step any tells user to enter whatever value
        if ( $args['input_type'] === 'number') {
            $html .= "<input type=\"number\" pattern=\"[0-9]*\" inputmode=\"numeric\" step=\"any\" name=\"amount_ecb\" value=\"{$this->last_amount}\">";                            
        }
        else {
            $html .= "<input type=\"text\" name=\"amount_ecb\" value=\"{$this->last_amount}\">";            
        }
        
        $html .= "</div>";

        $html .= "<div class=\"mcwp-selectfromto\">";

        $html .= "<div class=\"mcwp-selectfrom\">";
        $html .= "{$args['from_title']}:";

        //Order by lists
        if ( isset( $args['order_by']) ) {
            $ob = $args['order_by'];
            if ($ob !== null) 
            {
                //Sort by currency
                if ($ob === 'currency' || $ob === 'currencies')
                {
                     //Sort by currency
                    $currencydescriptions_arr = array();
                    foreach($currencies as $curr)  
                    {
                        if (isset($this->currency_description[(string)$curr])) 
                        {                        
                            $description = $this->currency_description[(string)$curr];
                            $currencydescriptions_arr[] = array(
                                                                                    'result' => (string)$curr,  //Name key as result so we can use existing sort by result-function
                                                                                    'desc' => $description
                                                                                );
                        }
                    }
                    
                    //Sort array by description
                    usort($currencydescriptions_arr, array( $this, 'sort_result') );
                    
                    //Remove current currencies-array and based on sort array above
                    //create a new one (use the sorted array)
                    $currencies = array();
                    foreach($currencydescriptions_arr as $c) 
                    {
                        $currencies[] = $c['result'];
                    }  
                    
                }
                else if ($ob === 'description' || $ob === 'currencydescription') 
                {
                    //Sort by (currency) description
                    $currencydescriptions_arr = array();
                    foreach($currencies as $curr)  
                    {
                        if (isset($this->currency_description[(string)$curr])) 
                        {                        
                            $description = $this->currency_description[(string)$curr];
                            $currencydescriptions_arr[] = array(
                                                                                    'currency' => (string)$curr,
                                                                                    'result' => $description //Name key as result so we can use existing sort by result-function
                                                                                );
                        }
                    }
                    
                    //Sort array by description
                    usort($currencydescriptions_arr, array( $this, 'sort_result') );
                    
                    //Remove current currencies-array and based on sort array above
                    //create a new one (use the sorted array)
                    $currencies = array();
                    foreach($currencydescriptions_arr as $c) 
                    {
                        $currencies[] = $c['currency'];
                    }                        
                }
            }
        }
        
        
        $html .= "<select name=\"from_ecb\">";
        foreach($currencies as $curr) 
        {
            $sel_from = " ";
            if ($curr == $from_curr) 
            {
                $sel_from = " selected=\"selected\" ";
            }

            //Currencies in lists are only shown if they exists in
            //array currency_description of this object
            if (isset($this->currency_description[(string)$curr])) 
            {                        
                $letters_curr = $curr;
                $desc_fromcurr = "";
                if ($args['show_currencydescription'] === 'yes') 
                {
                    $desc_fromcurr = " - " . $this->currency_description[(string)$curr];
                }
                if ($args['show_currencydescription'] === 'only') 
                {
                    $desc_fromcurr = $this->currency_description[(string)$curr];      
                    $letters_curr = "";
                }                

                $html .= "<option{$sel_from}value=\"{$curr}\">{$letters_curr}{$desc_fromcurr}</option>";                                
            }

        }
        $html .= "</select>";
        $html .= "</div>";

        $html .= "<div class=\"mcwp-selectto\">";                        
        $html .= "{$args['to_title']}:";                    
        $html .= "<select name=\"to_ecb\">";
        foreach($currencies as $curr) 
        {
            $sel_to = " ";
            if ($curr == $to_curr) 
            {
                $sel_to = " selected=\"selected\" ";
            }

            //Currencies in lists are only shown if they exists in
            //array currency_description of this object
            if (isset($this->currency_description[(string)$curr])) 
            {                                        
                $desc_tocurr = "";
                $letters_curr = $curr;                
                if ($args['show_currencydescription'] === 'yes') 
                {
                        $desc_tocurr = " - " . $this->currency_description[(string)$curr];
                }                        
                if ($args['show_currencydescription'] === 'only') 
                {
                    $desc_tocurr = $this->currency_description[(string)$curr];      
                    $letters_curr = "";
                }                     
                $html .= "<option{$sel_to}value=\"{$curr}\">{$letters_curr}{$desc_tocurr}</option>";                                
            }
        }
        $html .= "</select>";
        $html .= "</div>";                        

        $html .= "</div>";

        $html .= "<input class=\"mcwp-submit\" type=\"submit\" value=\"{$args['calculatebutton_title']}\" name=\"submit\">";
        $html .= "<input class=\"mcwp-decimals\" name=\"result_decimals\" type=\"hidden\" value=\"{$this->result_decimals}\" />";
        if ( $this->result_sanitize === true) 
        {
            $rs = 'yes';
        }
        else 
        {
            $rs = 'no';
        }
        $html .= "<input class=\"mcwp-sanitize\" name=\"result_sanitize\" type=\"hidden\" value=\"{$rs}\" />";
        $html .= "</form>";

        if ($this->last_result !== null) 
        {
            $html .= "<span class=\"mcwp-result\">";
            $html .= "<span class=\"mcwp-title\">{$result_title}:</span>";       
            $html .= "<span class=\"mcwp-currency\">";
            $html .= "{$this->last_amount} {$from_curr} = ";
            $html .= "</span>";
            $html .= "<span class=\"mcwp-tocurrency\">";
            $html .=  "{$this->last_result} {$to_curr}";
            $html .= "</span>";
            $html .= "</span>";
        }

        return $html;
    }

}
        
$mastercurrencywp = new mastercurrencywp();
$mastercurrencywp->init();
}