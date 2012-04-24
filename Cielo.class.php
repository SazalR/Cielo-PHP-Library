<?php
/**
 * @file
 * Cielo Webservice Consumer.
 *
 * @mainpage Cielo Webservice Consumer.
 */

include_once dirname(__FILE__) . '/BrazilCards.class.php'; 
include_once dirname(__FILE__) . '/cielo/cielo_xml_xsd.class.php';

/**
 * Implements BrazilCards class.
 */
class Cielo extends BrazilCards {
  /**
   * Cielo's URL for real transactions.
   */
  const CIELO_LIVE_URL = 'https://ecommerce.cbmp.com.br/servicos/ecommwsec.do';

  /**
   * Cielo's Sandbox URL for testing transactions.
   */
  const CIELO_SANDBOX_URL = 'https://qasecommerce.cielo.com.br/servicos/ecommwsec.do';

  /**
   * The membership number for when the merchants are going to collect card
   * details at their website.
   */
  const CIELO_SANDBOX_ON_SITE_MERCHANT_ID = '1006993069';

  /**
   * The token for when the merchants are going to collect card details at their
   * website.
   */
  const CIELO_SANDBOX_ON_SITE_MERCHANT_TOKEN = '25fbb99741c739dd84d7b06ec78c9bac718838630f30b112d033ce2e621b34f3';

  /**
   * The membership number for when the merchants redirect customers to
   * Cielo's website where the card details are going to be collected.
   */
  const CIELO_SANDBOX_OFF_SITE_MERCHANT_ID = '1001734898';

  /**
   * The token for when the merchants redirect customers to Cielo's website
   * where the card details are going to be collected.
   */
  const CIELO_SANDBOX_OFF_SITE_MERCHANT_TOKEN = 'e84827130b9837473681c2787007da5914d6359947015a5cdb2b8843db0fa832';
  
  /**
   * Cielo's Development Manual.
   */
  const CIELO_DEV_MANUAL = '1.5.6 Last Updated in October 2010';

  /**
   * I am not quite sure what this indicator is for. Just following the manual.
   */
  const CIELO_INDICADOR_DEFAULT = 1;

  /**
   * I am not quite sure what this indicator is for. Just following the manual.
   */
  const CIELO_INDICADOR_CVS_EMPTY = 0;

  /**
   * I am not quite sure what this indicator is for. Just following the manual.
   */
  const CIELO_INDICADOR_MASTERCARD = 1;

  /**
   * Used for installment payment terms.
   *
   * The merchant is the credit provider.
   */
  const CIELO_MERCHANT_IS_CREDITOR = '2';
  /**
   * Used for installment payment terms.
   *
   * A 3rd party (finantial institution) is the credit provider.
   */
  const CIELO_CARD_ISSUER_IS_CREDITOR = '3';
  
  /**
   * Defines the Card Mode, either Credit or Debit.
   *
   * Debit.
   */
  const CIELO_TYPE_DEBIT_CARD = 'A';
  
  /**
   * Defines the Card Mode, either Credit or Debit.
   *
   * Credit.
   */
  const CIELO_TYPE_CREDIT_CARD = '1';

  /**
   * Authentication Type.
   *
   * Authentication Only.
   */
  const CIELO_AUTHENTICATION_ONLY = 0;
    
  /**
   * Authentication Type.
   *
   * Authorize Only If Athenticated.
   */
  const CIELO_AUTHORIZE_ONLY_IF_AUTHENTICATED = 1;
    
  /**
   * Authentication Type.
   *
   * Authorize Either Authenticated Or Not.
   */
  const CIELO_AUTHORIZE_EITHER_AUTHENTICATED_OR_NOT = 2;
    
  /**
   * Authentication Type.
   *
   * Skip Authentication And Go Straight To Authorization.
   */
  const CIELO_SKIP_AUTHENTICATION = 3;

  /**
   * Defines the International Currency code for Brazilian Real.
   */
  const CIELO_CURRENCY_CODE_BRL = 986;
    
  /**
   * Defines the Cielo's languange interface.
   *
   * Portuguese.
   */
  const CIELO_LANG_PT = 'PT';

   
  /**
   * Defines the Cielo's languange interface.
   *
   * Spanish.
   */
  const CIELO_LANG_ES = 'ES';
    
  /**
   * Defines the Cielo's languange interface.
   *
   * English.
   */
  const CIELO_LANG_EN = 'EN';
    
  /**
   * Defines the Card Flag.
   *
   * Master Card.
   */
  const CIELO_FLAG_MASTERCARD = 'mastercard';
    
  /**
   * Defines the Card Flag.
   *
   * Visa.
   */
  const CIELO_FLAG_VISA = 'visa';
    
  /**
   * Defines the Card Flag.
   *
   * Elo.
   */
  const CIELO_FLAG_ELO = 'elo';
    
  /**
   * Defines the Remote Status of the Transaction.
   */
  const CIELO_TRANSACTION_CREATED = 0;
    
  /**
   * Defines the Remote Status of the Transaction.
   */
  const CIELO_IN_PROGRESS = 1;
    
  /**
   * Defines the Remote Status of the Transaction.
   */
  const CIELO_AUTHENTICATED = 2;
    
  /**
   * Defines the Remote Status of the Transaction.
   */
  const CIELO_NOT_AUTHENTICATED = 3;
    
  /**
   * Defines the Remote Status of the Transaction.
   *
   * Authorized or still to be captured.
   */
  const CIELO_AUTHORIZED = 4;
    
  /**
   * Defines the Remote Status of the Transaction.
   */
  const CIELO_BEING_AUTHENTICATED = 10;
    
  /**
   * Defines the Remote Status of the Transaction.
   *
   * Success.
   */
  const CIELO_CAPTURED = 6;
    
  /**
   * Defines the Remote Status of the Transaction.
   *
   * Failure.
   */
  const CIELO_AUTHORIZATION_DENIED = 5;
    
  /**
   * Defines the Remote Status of the Transaction.
   *
   * Failure.
   */
  const CIELO_NOT_CAPTURED = 8;
    
  /**
   * Defines the Remote Status of the Transaction.
   */
  const CIELO_VOIDED = 9;

  /**
   * Holds the xml object.
   */
  public $envelope;

  public function setUp() {
    // Webservice settings.
    // Define defaut values for both test and live services.
    $test = array(
      'url' => self::CIELO_SANDBOX_URL,
      'merchant' => self::CIELO_SANDBOX_ON_SITE_MERCHANT_ID,
      'merchant_chave' => self::CIELO_SANDBOX_ON_SITE_MERCHANT_TOKEN,
      'cielo' => self::CIELO_SANDBOX_OFF_SITE_MERCHANT_ID,
      'cielo_chave' => self::CIELO_SANDBOX_OFF_SITE_MERCHANT_TOKEN,
    );

    $live = array(
      'url' => self::CIELO_LIVE_URL,
    );

    // Sets the test credentials as default.
    $this->ws = $test;
    
    // Set the path to the CA file (SSL Public key) for testing enviroment.
    $this->ws['curl_pubKey'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cielo' . DIRECTORY_SEPARATOR . 'VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt';

    if (!$this->is_test) {
      // Get the live preset.
      $this->ws = $live;
      // Uses CA from Trust Store.
      $this->ws['curl_pubKey'] = FALSE;
    }

    // Manual version.
    $this->ws['manual_version'] = self::CIELO_DEV_MANUAL;

    // Validate public key for curl requests.
    $this->ws['curl_use_ssl'] = TRUE;

    // Extends the envelope object.
    $this->envelope = new cielo_xml_xsd();

    // Set default values for xsd envelopes.
    $this->envelope->request_data = array (
      // Cielo's xsd version.
      'xsd_version'   => '1.1.0',
      // Currency code, defaulted to BRL.
      'currency_code' => self::CIELO_CURRENCY_CODE_BRL,
      // Language code.
      'language_code' => self::CIELO_LANG_PT,
      // Date and time.
      'date_time' => date("Y-m-d\TH:i:s"),
      'tid' => '',
      // This determine if an redirection for authentication is made right away
      // once we get the value of $response['url-autenticacao'].
      'autoRedirect'  => FALSE,
    );

    // Url for returning from cielo after authentication has taken place.
    $http = 'http://';
    $domain_name = $_SERVER["SERVER_NAME"];
    
    if ($_SERVER["SERVER_PORT"] == 443) {
      // Adds a 's' between the p and :.
      $http = substr_replace($http, 's', 4, 0);
    }
    elseif ($_SERVER["SERVER_PORT"] <> 80) {
      // Append the port number to the end of the domain name.
      $domain_name .= ':'.$_SERVER["SERVER_PORT"];
    }
    // Assemble the returning url.
    $this->envelope->request_data['return_url'] = $http . $domain_name . $_SERVER["PHP_SELF"] . '?order=' . $this->order['pedido'];

    // Format the Purcharse Order value.
    // Remove any dot or comma that it eventually might have.
    $this->order['TotalAmount'] = str_replace(',', '', $this->order['TotalAmount']);
    $this->order['TotalAmount'] = str_replace('.', '', $this->order['TotalAmount']);

    // Set up payment attributes.
    self::setPaymentAttributes();

    // Construct the envelope object.
    $this->envelope->setObject($this);
  }

  /**
   * Requests an authorization to the remote server. A new transaction is
   * created.
   */
  public function authorize() {

    // Create a tid.
    if ($this->parameters['CardHandling']) {

      // Merchant is collecting card details and sending them through.
      if ($this->parameters['Authenticate']) {
        // Request a new transaction.
        self::httprequest($this->envelope->requisicao_transacao());

        // The authentication url wont be present if the merchant has opted to
        // not authenticate the card hold so that's the why we gotta check it.
        if (isset($this->response['url-autenticacao']) && $this->envelope->request_data['autoRedirect']) {
          // Redirect browser to cielo for authenticating the card holder
          header('Location: ' . $this->response['url-autenticacao']);  
        }

        // Once the browser is redirected back from cielo, the application will
        // have to perform a follow up on this transaction to find out if it has
        // been authorized or not by calling $myObject->followUp().
      }
      else {
        // Card holder wont be authenticated.
        // Request a new transaction.
        self::httprequest($this->envelope->requisicao_tid());

        // Request authorization.
        $this->envelope->request_data['tid'] = $this->response['tid'];
        self::httprequest($this->envelope->requisicao_autorizacao_portador());
      }
    }
    else {
      // Customers will be asked to provide their card details at cielo's
      // website.
      self::httprequest($this->envelope->requisicao_transacao());

      if (isset($this->response['url-autenticacao']) && $this->envelope->request_data['autoRedirect']) {
        // Redirect browser to cielo for collecting buyer's card details and
        // performing authentication.
        header( 'Location: ' . $this->response['url-autenticacao']);
      }
    }
  }
  
  /**
   * Checks if there is a transaction Id available, if so then it requests
   * details about that transaction to the remote server.
   */
  public function followUp() {
    // Check if there is a tid available.
    if (empty($this->envelope->request_data['tid'])) {
      $this->setWarning(array('follow_up', "Could not do the follow up because request_data['tid'] property is not set."));  
    }
    else {
      self::httprequest($this->envelope->requisicao_consulta());      
    }
  }
  /**
   * Alias of capturePreAuthorize() but it will always attempts capturing the
   * full amount previously authorized if there is any.
   *
   * To capture an amount smaller than the one previously authorized you
   * should then call $object->capturePreAuthorize($mySmallerAmount)
   */
  public function capture() {
    self::capturePreAuthorize();
  }
  /**
   * Requests the capturing of a transaction previously authorized.
   *
   * @param string $amount
   *   Defaut is empty which captures the full amount available for that
   *   transaction. If a value is passed then it will try to capture the value
   *   just passed.
   */
  public function capturePreAuthorize($amount = '') {
    if (empty($amount)) {
      // Capture its total.
      $this->envelope->request_data['captureAmount'] = $this->order['TotalAmount'];
    }
    elseif ($amount > $this->order['TotalAmount']) {
      // Even when this check does not fail, the webservice still might deny it
      // if remaining balance from previous partial captures is less than the
      // amount of this capturing attempt.
      // Throw a warning. 
      $this->setWarning(array('capturePreAuthorize', "Amount to be captured can't be greater than the amount previously authorized."));
      break;
    }
    else {
      // Partial capture or $amount represents 100% of the authorized amount
      $this->envelope->request_data['captureAmount'] = $amount;
    }
    
    // Check if there is a tid available
    if (empty($this->envelope->request_data['tid'])) {
      $this->setWarning(array('capturePreAuthorize', 'Could not do the capturing because request_data[\'tid\'] property is not set.'));  
    }
    else {
      self::httprequest($this->envelope->requisicao_captura());
    }
  }

  /**
   * Checks if there is a transaction Id available, if so then it requests a
   * cancelation of that transaction to the remote server.
   *
   * Voiding a transaction can only be done in the same day it was captured.
   * This is a restriction imposed by Cielo.
   */
  public function voidTransaction() {    
    // Check if there is a tid available
    if (empty($this->envelope->request_data['tid'])) {
      $this->setWarning(array('capturePreAuthorize', "Could not do the voiding because request_data['tid'] property is not set."));
    }
    else {
      self::httprequest($this->envelope->requisicao_cancelamento());
    }
  }

  /**
   * Helper function.
   */
  private function setPaymentAttributes() {

    // Save payment attributes on parameters property.
    $this->parameters = $paymentAttributes = $this->arguments['payment'];
    
    if(isset($this->parameters['CardType']) && $this->parameters['CardType'] == self::CIELO_TYPE_DEBIT_CARD){
      // Make sure authentication will always be switched on when card type is
      // Debit.
      $this->parameters['Authenticate'] == TRUE;
    }
    
    /**
     * Set Default values for parameters.
     * #expected holds the list of valid values.
     *   If the value set doesn't match any of the expected values then its
     *   default will prevail.
     **/
    $checkList = array(
      'Installments' => array(
        '#default' => 1,
      ),
      'Creditor' => array(
        '#default' => self::CIELO_CARD_ISSUER_IS_CREDITOR,
        '#expected' => array(self::CIELO_MERCHANT_IS_CREDITOR, self::CIELO_CARD_ISSUER_IS_CREDITOR),
      ),
      'CardType' => array(
        '#default'  => self::CIELO_TYPE_CREDIT_CARD,
        '#expected' => array(self::CIELO_TYPE_DEBIT_CARD, self::CIELO_TYPE_CREDIT_CARD),
      ),
      'AutoCapturer' => array(
        '#default' => 'true',
        '#expected' => array('false', 'true'),
      ),
      'AuthorizationType' => array(
        '#default'  => self::CIELO_AUTHORIZE_EITHER_AUTHENTICATED_OR_NOT,
        '#expected' => array(
          self::CIELO_AUTHENTICATION_ONLY,
          self::CIELO_AUTHORIZE_ONLY_IF_AUTHENTICATED,
          self::CIELO_AUTHORIZE_EITHER_AUTHENTICATED_OR_NOT,
          self::CIELO_SKIP_AUTHENTICATION,
        ),
      ),
      'Authenticate' => array(
        '#default'  => 'true',
        '#expected' => array('false', 'true'),
      ),
    );

    foreach($checkList as $attribute => $settings) {
      // If parameter was set but is not one of the expected values then we
      // override it with its default value.
      if (isset($paymentAttributes[$attribute]) && isset($settings['#expected']) && !in_array($paymentAttributes[$attribute], $settings['#expected'])) {
        $this->parameters[$attribute]  = $settings['#default'];
        // Set a warning.
        $this->setWarning(array("parameter_sent", "Parameter $attribute has an unexpected value and therefore has been ignored. The default value ". $settings['#default'] . " was used instead. Double check your application."));
      }

      // If paramenter was not set and there is a default value for it, then we
      // set its default.
      if (!isset($paymentAttributes[$attribute]) && isset($settings['#default'])) {
        $this->parameters[$attribute]  = $settings['#default'];
        // Set a warning.
        $this->setWarning(array("parameter_sent_empty", "Parameter $attribute has an empty value and its default value ". $settings['#default'] . " was used. Double check your application."));
      }
    }

    // Make sure the string boolean value 'true' wont be represented by 1.
    if ($this->parameters['AutoCapturer'] === 1) {
      $this->parameters['AutoCapturer'] = 'true';  
    }

    // Define InstallmentType.
    // One single payment.
    // Either A (Debit Card) or 1 (Credit Card).
    $this->parameters['InstallmentType'] = $this->parameters['CardType'];
    // Payment on installment term.
    if ($this->parameters['Installments'] > 1) {
      // Define who has the guts to be the creditor.
      // 2 (merchant) or 3 (cielo).
      $this->parameters['InstallmentType'] = $this->parameters['Creditor'];
    }
    
    // Check if card details are being collected by the merchant.
    // Set Default as FALSE.
    $this->parameters['CardHandling'] = FALSE;
    if (!empty($paymentAttributes['CardNumber'])) {
      $this->parameters['CardHandling'] = TRUE;

      // Set default indicator as 1.
      $this->envelope->request_data['indicador'] = self::CIELO_INDICADOR_DEFAULT;
   
      if (empty($paymentAttributes['CVC'])) {
        $this->envelope->request_data['indicador'] = self::CIELO_INDICADOR_CVS_EMPTY;
      }
      elseif ($paymentAttributes['CardFlag'] == 'mastercard') {
        $this->envelope->request_data['indicador'] = self::CIELO_INDICADOR_MASTERCARD;
      }
    }
    
    if ($this->is_test) {
      // This is a test environment so we need to define values for filiacao
      // and chave.

      // Default: merchant collects card details from its customers.
      $this->membership['filiacao'] = $this->ws['merchant'];
      $this->membership['chave']  = $this->ws['merchant_chave'];

      if (!$this->parameters['CardHandling']) {
        // Customers are asked to provide their card details at cielo's website.
        $this->membership['filiacao'] = $this->ws['cielo'];
        $this->membership['chave']  = $this->ws['cielo_chave'];
      }
    }
  }

  /**
   * Set transaction Id.
   * @param String $tid
   *  The transaction Id that came obtained from a provious server response.
   */
  public function setTid($tid) {
    $this->envelope->request_data['tid'] = $tid;
  }
  
  /**
   * Set Currency.
   * @param String $currency
   *   The ISO 4217 currency code with 3 digits number.
   */
  public function setCurrency($currency) {
    $this->envelope->request_data['currency_code'] = $currency;
  }

  /**
   * Set Language Code.
   *
   * @param String $lang
   *   Expected codes are: PT, EN or ES
   */
  public function setLanguage($lang) {
    $this->envelope->request_data['language_code'] = $lang;
  }

  /**
   * Set Returning URL.
   * 
   * @param String $url
   *   The script url for concluding the payment processing after returning from
   *   Cielo.
   */
  public function setReturnUrl($url) {
    $this->envelope->request_data['return_url'] = $url;
  }

  /**
   * Set Auto Redirect.
   * 
   * @param Boolean $value
   *  Determine whether or not the browser should be redirected to
   *  Cielo right after a response in which redirection for further processing
   *  is required.
   */
  public function setAutoRedirect($value = TRUE) {
    $this->envelope->request_data['autoRedirect'] = $value;
  }
  
  /**
   * Set Location of the CA file (public key) for SSL validadion.
   * 
   * @param String $location
   *  The absolute location and file name of the CA file.
   *  If no path is sent then the system uses the Trust Store CA.
   */
  public function setCertificateLocation($location = FALSE) {
    $this->ws['curl_pubKey'] = $location;
  }
  
  /**
   * Helper function.
   *
   * It makes xml request calls to cielo's webservice.
   */  
  private function httprequest($xsd) {
    $xsd = 'mensagem=' . $xsd;
    
    $sessao_curl = curl_init();
    curl_setopt($sessao_curl, CURLOPT_URL, $this->ws['url']);
    curl_setopt($sessao_curl, CURLOPT_FAILONERROR, true);
    curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYPEER, $this->ws['curl_use_ssl']);
    curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYHOST, 2);
    // Check if there is a custom CA available.
    if ($this->ws['curl_pubKey']) {
      curl_setopt($sessao_curl, CURLOPT_CAINFO, $this->ws['curl_pubKey']);
    }
    curl_setopt($sessao_curl, CURLOPT_SSLVERSION, 3);
    curl_setopt($sessao_curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($sessao_curl, CURLOPT_TIMEOUT, 40);
    curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($sessao_curl, CURLOPT_POST, true);
    curl_setopt($sessao_curl, CURLOPT_POSTFIELDS, $xsd);

    $resultado = curl_exec($sessao_curl);
    
    if ($resultado) {
      $this->response = simplexml_load_string($resultado);

      // Convert the simplexml objects into arrays
      $this->response = (array) $this->response;

      foreach($this->response as $key => $value) {
        if (is_object($value)) {
          $this->response[$key] = (array) $value;
        }
      }
    }
    else {
      $this->setWarning(array('curl_error', '<pre>' . curl_error($sessao_curl) . '</pre>'));
    }
    // Close Curl session.
    curl_close($sessao_curl);
  }
  
  /**
   * Static method that returns an array containing all the available flags.
   * 
   * @param Boolean $valued
   *   Whether or not the returned array is valued with the card flag names.
   * @param String $function_name
   *   The name of your custom callback function in which the returning result
   *   will be sent through.
   */  
  static public function get_card_flags($valued = TRUE, $function_name = NULL) {
    if ($valued) {
      $result = array(
        self::CIELO_FLAG_MASTERCARD => 'Mastercard',
        self::CIELO_FLAG_VISA => 'Visa',
        self::CIELO_FLAG_ELO => 'Elo',
      );
    }
    else {
      $result = array(
        self::CIELO_FLAG_MASTERCARD,
        self::CIELO_FLAG_VISA,
        self::CIELO_FLAG_ELO,
      );
    }
    if ($function_name && function_exists($function_name)) {
      return $function_name($result);
    }
    else {
      return $result;
    }
  }
  
  /**
   * Static method that returns an array containing all the Authorization Types.
   * 
   * @param Boolean $valued
   *   Whether or not the returned array is valued with the Authorization Type
   *   names.
   * @param String $function_name
   *   The name of your custom callback function in which the returning result
   *   will be sent through.
   */  
  static public function get_authorization_options($valued = TRUE, $function_name = NULL) {
    if ($valued) {
      $result = array(
        self::CIELO_AUTHENTICATION_ONLY => 'Authentication only. (use this only if you know what you are doing.)',
        self::CIELO_AUTHORIZE_ONLY_IF_AUTHENTICATED => 'Authorize only if authenticaded',
        self::CIELO_AUTHORIZE_EITHER_AUTHENTICATED_OR_NOT => 'Authorize either authenticated or not',
        self::CIELO_SKIP_AUTHENTICATION => 'Skip authentication and go straight to authorization',
      );
    }
    else {
      $result = array(
        self::CIELO_AUTHENTICATION_ONLY,
        self::CIELO_AUTHORIZE_ONLY_IF_AUTHENTICATED,
        self::CIELO_AUTHORIZE_EITHER_AUTHENTICATED_OR_NOT,
        self::CIELO_SKIP_AUTHENTICATION,
      );
    }
    if ($function_name && function_exists($function_name)) {
      return $function_name($result);
    }
    else {
      return $result;
    }
  }
  
  /**
   * Static method that returns an array containing all the languages supported
   * by Cielo.
   * 
   * @param Boolean $valued
   *   Whether or not the returned array is valued with the language names.
   * @param String $function_name
   *   The name of your custom callback function in which the returning result
   *   will be sent through.
   */  
  static public function get_languages($valued = TRUE, $function_name = NULL) {
    if ($valued) {
      $result = array(
        self::CIELO_LANG_PT => 'Portuguese',
        self::CIELO_LANG_EN => 'English',
        self::CIELO_LANG_ES => 'Spanish',
      );
    }
    else {
      $result = array(
        self::CIELO_LANG_PT,
        self::CIELO_LANG_EN,
        self::CIELO_LANG_ES,
      );
    }
    if ($function_name && function_exists($function_name)) {
      return $function_name($result);
    }
    else {
      return $result;
    }
  }

  /**
   * Static method that returns an array containing all the languages supported
   * by Cielo.
   * 
   * @param Boolean $valued
   *   Whether or not the returned array is valued with the language names.
   * @param String $function_name
   *   The name of your custom callback function in which the returning result
   *   will be sent through.
   */  
  static public function get_installment_creditor_options($valued = TRUE, $function_name = NULL) {
    if ($valued) {
      $result = array(
        self::CIELO_MERCHANT_IS_CREDITOR => 'Merchant is the creditor.',
        self::CIELO_CARD_ISSUER_IS_CREDITOR => 'Card issuer / Cielo are the creditors.',
      );
    }
    else {
      $result = array(
        self::CIELO_MERCHANT_IS_CREDITOR,
        self::CIELO_CARD_ISSUER_IS_CREDITOR,
      );
    }
    if ($function_name && function_exists($function_name)) {
      return $function_name($result);
    }
    else {
      return $result;
    }
  }

}
