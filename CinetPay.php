<?php

class CinetPay {

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_page_action = "PAYMENT";

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_payment_config = "SINGLE";

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_version = "V1";

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_language = "fr";

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_currency = "CFA";

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_trans_date = null;

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_trans_id = null;

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_designation = null;

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_custom = null;

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_amount = null;

    /**
     * An indentifier
     * @var string 
     */
    protected $_cfg_apikey = null;

    /**
     * An indentifier
     * @var string 
     */
    public $_cfg_cpm_site_id = null;

    /**
     * An indentifier
     * @var string 
     */
    protected $_signatureUri = null;

    /**
     * An indentifier
     * @var string 
     */
    public $_cashDeskUri = null;

    /**
     * An indentifier
     * @var string 
     */
    protected $_checkPayStatusUri = null;

    /**
     * An indentifier
     * @var string 
     */
    protected $_webSiteUri = null;
    public $_signature = null;
    public $_cpm_site_id = null;
    public $_cpm_amount = null;
    public $_cpm_trans_id = null;
    public $_cpm_custom = null;
    public $_cpm_currency = null;
    public $_cpm_payid = null;
    public $_cpm_payment_date = null;
    public $_cpm_payment_time = null;
    public $_cpm_error_message = null;
    public $_payment_method = null;
    public $_cpm_phone_prefixe = null;
    public $_cel_phone_num = null;
    public $_cpm_ipn_ack = null;
    public $_created_at = null;
    public $_updated_at = null;
    public $_cpm_result = null;
    public $_cpm_trans_status = null;
    public $_cpm_designation = null;
    public $_buyer_name = null;

    const URI_WEBSITE_PROD = 'www.prod.cinetpay.com';
    const URI_WEBSITE_DEV = 'www.sandbox.cinetpay.com';
    const URI_CASH_DESK_PROD = 'secure.cinetpay.com';
    const URI_CASH_DESK_DEV = 'secure.sandbox.cinetpay.com';
    const URI_GET_SIGNATURE_PROD = 'api.cinetpay.com/v1/?method=getSignatureByPost';
    const URI_GET_SIGNATURE_DEV = 'api.sandbox.cinetpay.com/v1/?method=getSignatureByPost';
    const URI_CHECK_PAY_STATUS_PROD = 'api.cinetpay.com/v1/?method=checkPayStatus';
    const URI_CHECK_PAY_STATUS_DEV = 'api.sandbox.cinetpay.com/v1/?method=checkPayStatus';

    /**
     *  If true, an SSL secure connection (port 443) is used for the post back 
     *  as recommended by cinetpay. If false, a standard HTTP (port 80) connection
     *  is used. Default true.
     *
     *  @var boolean
     */
    public $_use_ssl = false;

    /**
     *  If true, the cinetpay sandbox URI www.sandbox.cinetpay.com is used for the
     *  post back. If false, the live URI www.cinetpay.com is used. Default false.
     *
     *  @var boolean
     */
    public $_use_sandbox = false;

    public function CinetPay($site_id, $apikey, $mode = "PROD") {

        if ($mode == "PROD") {
            $this->_use_sandbox = false;
            $this->_use_ssl = true;
        } else {
            $this->_use_sandbox = true;
        }

        $this->_cfg_cpm_site_id = $site_id;
        $this->_cfg_apikey = $apikey;
        $htpp_prefixe = ($this->_use_ssl) ? 'https://' : 'http://';
        $this->_cashDeskUri = $htpp_prefixe . $this->getCashDeskHost();
        $this->_signatureUri = $htpp_prefixe . $this->getSignatureHost();
        $this->_checkPayStatusUri = $htpp_prefixe . $this->getCheckPayStatusHost();
        $this->_webSiteUri = $htpp_prefixe . $this->getWebSiteHost();
    }

    private function getWebSiteHost() {
        if ($this->_use_sandbox)
            return self::URI_WEBSITE_DEV;
        else
            return self::URI_WEBSITE_PROD;
    }

    private function getCashDeskHost() {
        if ($this->_use_sandbox)
            return self::URI_CASH_DESK_DEV;
        else
            return self::URI_CASH_DESK_PROD;
    }

    private function getSignatureHost() {
        if ($this->_use_sandbox)
            return self::URI_GET_SIGNATURE_DEV;
        else
            return self::URI_GET_SIGNATURE_PROD;
    }

    private function getCheckPayStatusHost() {
        if ($this->_use_sandbox)
            return self::URI_CHECK_PAY_STATUS_DEV;
        else
            return self::URI_CHECK_PAY_STATUS_PROD;
    }

    public function displayPayButton($formName, $btnType = 1, $btnWidth = "120px", $btnHeight = "") {
        print $this->getPayButton($formName, $btnType, $btnWidth, $btnHeight);
    }

    public function getOnlyPayButtonToSubmit($formName, $btnType = 1, $btnWidth = "120px", $btnHeight = "") {
        $w = (!empty($btnWidth)) ? "width=$btnWidth" : "";
        $h = (!empty($btnHeight)) ? "height=$btnHeight" : "";
        $btnType = (int) $btnType;

        if (!empty($formName) && $btnType == 1)
            $btn = "<input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/acheter.png' onclick='javascript:document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' />";
        elseif (!empty($formName) && $btnType == 2)
            $btn = "<input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/payer.png' onclick='javascript:document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' />";
        elseif (!empty($formName) && $btnType == 3)
            $btn = "<input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/faire-don.png' onclick='javascript:document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' />";
        elseif (!empty($formName) && $btnType == 4)
            $btn = "<input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/faire-un-don.png' onclick='javascript:document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' />";
        elseif (!empty($formName) && $btnType == 5)
            $btn = "<input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/payer-avec-cinetpay.png' onclick='javascript:document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' />";
        else
            $btn = "<input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/acheter.png' onclick='javascript:document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' />";

        return $btn;
    }

    public function getPayButton($formName, $btnType = 1, $btnWidth = "120px", $btnHeight = "") {

        $signature = $this->getSignature();

        if (empty($this->_cfg_apikey))
            throw new Exception("Erreur: ApiKey non definie");
        if (empty($this->_cashDeskUri))
            throw new Exception("Erreur: Url de paiement non definie");
        if (empty($this->_cfg_cpm_site_id))
            throw new Exception("Erreur: Site ID non definie");
        if (empty($this->_cfg_cpm_currency))
            throw new Exception("Erreur: Devise non definie");
        if (empty($this->_cfg_cpm_page_action))
            throw new Exception("Erreur: Page action non definie");
        if (empty($this->_cfg_cpm_payment_config))
            throw new Exception("Erreur: Payment config non definie");
        if (empty($this->_cfg_cpm_version))
            throw new Exception("Erreur: Version non definie");
        if (empty($this->_cfg_cpm_language))
            throw new Exception("Erreur: Langue non definie");
        if (empty($this->_cfg_cpm_trans_date))
            throw new Exception("Erreur: Date de la transaction non definie");
        if (empty($this->_cfg_cpm_trans_id))
            throw new Exception("Erreur: ID de la transaction non definie");
        if (empty($this->_cfg_cpm_designation))
            throw new Exception("Erreur: Designation de la transaction non definie");
        if (empty($this->_cfg_cpm_amount))
            throw new Exception("Erreur: Montant de la transaction non definie");
        if (empty($this->_signature))
            throw new Exception("Erreur: Signature de la transaction non trouvee");
        if (empty($formName))
            throw new Exception("Erreur: Nom du formulaire non definie");

        $form = "<form id='" . $formName . "' name='" . $formName . "' action='" . $this->_cashDeskUri . "' method='post'>";
        $form .= "<input type='hidden' name='apikey' value='" . $this->_cfg_apikey . "'>";
        $form .= "<input type='hidden' name='cpm_site_id' value='" . $this->_cfg_cpm_site_id . "'>";
        $form .= "<input type='hidden' name='cpm_currency' value='" . $this->_cfg_cpm_currency . "'>";
        $form .= "<input type='hidden' name='cpm_page_action' value='" . $this->_cfg_cpm_page_action . "'>";
        $form .= "<input type='hidden' name='cpm_payment_config' value='" . $this->_cfg_cpm_payment_config . "'>";
        $form .= "<input type='hidden' name='cpm_version' value='" . $this->_cfg_cpm_version . "'>";
        $form .= "<input type='hidden' name='cpm_language' value='" . $this->_cfg_cpm_language . "'>";
        $form .= "<input type='hidden' name='cpm_trans_date' value='" . $this->_cfg_cpm_trans_date . "'>";
        $form .= "<input type='hidden' name='cpm_trans_id' value='" . $this->_cfg_cpm_trans_id . "'>";
        $form .= "<input type='hidden' name='cpm_designation' value='" . $this->_cfg_cpm_designation . "'>";
        $form .= "<input type='hidden' name='cpm_amount' value='" . $this->_cfg_cpm_amount . "'>";
        $form .= "<input type='hidden' name='signature' value='" . $this->_signature . "'>";

        if (!empty($this->_cfg_cpm_custom))
            $form .= "<input type='hidden' name='cpm_custom' value='" . $this->_cfg_cpm_custom . "'>";

        $form .= $this->getOnlyPayButtonToSubmit($formName, $btnType, $btnWidth, $btnHeight);

        return $form;
    }

    public function submitCinetPayForm() {

        $signature = $this->getSignature();

        if (empty($this->_cfg_apikey))
            throw new Exception("Erreur: ApiKey non definie");
        if (empty($this->_cashDeskUri))
            throw new Exception("Erreur: Url de paiement non definie");
        if (empty($this->_cfg_cpm_site_id))
            throw new Exception("Erreur: Site ID non definie");
        if (empty($this->_cfg_cpm_currency))
            throw new Exception("Erreur: Devise non definie");
        if (empty($this->_cfg_cpm_page_action))
            throw new Exception("Erreur: Page action non definie");
        if (empty($this->_cfg_cpm_payment_config))
            throw new Exception("Erreur: Payment config non definie");
        if (empty($this->_cfg_cpm_version))
            throw new Exception("Erreur: Version non definie");
        if (empty($this->_cfg_cpm_language))
            throw new Exception("Erreur: Langue non definie");
        if (empty($this->_cfg_cpm_trans_date))
            throw new Exception("Erreur: Date de la transaction non definie");
        if (empty($this->_cfg_cpm_trans_id))
            throw new Exception("Erreur: ID de la transaction non definie");
        if (empty($this->_cfg_cpm_designation))
            throw new Exception("Erreur: Designation de la transaction non definie");
        if (empty($this->_cfg_cpm_amount))
            throw new Exception("Erreur: Montant de la transaction non definie");
        if (empty($this->_signature))
            throw new Exception("Erreur: Signature de la transaction non trouvee");

        $form = "<form id='form_paiement_cinetpay' name='form_paiement_cinetpay' action='" . $this->_cashDeskUri . "' method='post'>";
        $form .= "<input type='hidden' name='apikey' value='" . $this->_cfg_apikey . "'>";
        $form .= "<input type='hidden' name='cpm_site_id' value='" . $this->_cfg_cpm_site_id . "'>";
        $form .= "<input type='hidden' name='cpm_currency' value='" . $this->_cfg_cpm_currency . "'>";
        $form .= "<input type='hidden' name='cpm_page_action' value='" . $this->_cfg_cpm_page_action . "'>";
        $form .= "<input type='hidden' name='cpm_payment_config' value='" . $this->_cfg_cpm_payment_config . "'>";
        $form .= "<input type='hidden' name='cpm_version' value='" . $this->_cfg_cpm_version . "'>";
        $form .= "<input type='hidden' name='cpm_language' value='" . $this->_cfg_cpm_language . "'>";
        $form .= "<input type='hidden' name='cpm_trans_date' value='" . $this->_cfg_cpm_trans_date . "'>";
        $form .= "<input type='hidden' name='cpm_trans_id' value='" . $this->_cfg_cpm_trans_id . "'>";
        $form .= "<input type='hidden' name='cpm_designation' value='" . $this->_cfg_cpm_designation . "'>";
        $form .= "<input type='hidden' name='cpm_amount' value='" . $this->_cfg_cpm_amount . "'>";
        $form .= "<input type='hidden' name='signature' value='" . $this->_signature . "'>";

        if (!empty($this->_cfg_cpm_custom))
            $form .= "<input type='hidden' name='cpm_custom' value='" . $this->_cfg_cpm_custom . "'>";

        $form .= '<script type="text/javascript">document.forms["form_paiement_cinetpay"].submit();</script>'; //We submit data here

        print $form;
    }

    public function getSignature() {

        $data = array();
        $data = $this->getPaySignatureArray();
        $flux_json = $this->callCinetpayWsMethod($data, $this->_signatureUri);
        if ($flux_json === false)
            throw new Exception("Un probleme est survenu lors de l'appel du WS !");

        $this->_signature = json_decode($flux_json, true);
        if (is_array($this->_signature)) {
            if (!isset($this->_signature['status']))
                $message = 'La plateforme CINETPAY est temporairement indisponible.';
            else
                $message = 'Une erreur est survenue, Code: ' . $this->_signature['status']['code'] . ', Message: ' . $this->_signature['status']['message'];

            throw new Exception($message);
        }
        return $this->_signature;
    }

    public function getPayStatus() {

        $data = array();
        $data = $this->getPayStatusArray();

        $flux_json = $this->callCinetpayWsMethod($data, $this->_checkPayStatusUri);
        if ($flux_json === false)
            throw new Exception("Un probleme est survenu lors de l'appel du WS !");

        $decodeText = html_entity_decode($flux_json);
        $array_flux_json = json_decode($decodeText, true);

        $this->_cpm_site_id = $array_flux_json['transaction']['cpm_site_id'];
        $this->_signature = $array_flux_json['transaction']['signature'];
        $this->_cpm_amount = $array_flux_json['transaction']['cpm_amount'];
        $this->_cpm_trans_id = $array_flux_json['transaction']['cpm_trans_id'];
        $this->_cpm_custom = $array_flux_json['transaction']['cpm_custom'];
        $this->_cpm_currency = $array_flux_json['transaction']['cpm_currency'];
        $this->_cpm_payid = $array_flux_json['transaction']['cpm_payid'];
        $this->_cpm_payment_date = $array_flux_json['transaction']['cpm_payment_date'];
        $this->_cpm_payment_time = $array_flux_json['transaction']['cpm_payment_time'];
        $this->_cpm_error_message = $array_flux_json['transaction']['cpm_error_message'];
        $this->_payment_method = $array_flux_json['transaction']['payment_method'];
        $this->_cpm_phone_prefixe = $array_flux_json['transaction']['cpm_phone_prefixe'];
        $this->_cel_phone_num = $array_flux_json['transaction']['cel_phone_num'];
        $this->_cpm_ipn_ack = $array_flux_json['transaction']['cpm_ipn_ack'];
        $this->_created_at = $array_flux_json['transaction']['created_at'];
        $this->_updated_at = $array_flux_json['transaction']['updated_at'];
        $this->_cpm_result = $array_flux_json['transaction']['cpm_result'];
        $this->_cpm_trans_status = $array_flux_json['transaction']['cpm_trans_status'];
        $this->_cpm_designation = $array_flux_json['transaction']['cpm_designation'];
        $this->_buyer_name = $array_flux_json['transaction']['buyer_name'];

        if ($this->_cpm_site_id != $this->_cfg_cpm_site_id)
            throw new Exception("Desol&eacute;, aucune donn&eacute;e trouv&eacute;e !");

        return true;
    }

    private function callCinetpayWsMethod($params, $url) {
        try {
            // Build Http query using params
            $query = http_build_query($params);
            // Create Http context details
            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen($query) . "\r\n" .
                    "User-Agent:MyAgent/1.0\r\n",
                    'method' => "POST",
                    'content' => $query,
                ),
            );
            // Create context resource for our request
            $context = stream_context_create($options);
            // Read page rendered as result of your POST request
            $result = file_get_contents(
                    $url, // page url
                    false, $context);
            return trim($result);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function setTransId($id) {
        $this->_cfg_cpm_trans_id = $id;
        return $this;
    }

    public function setDesignation($designation) {
        $this->_cfg_cpm_designation = $designation;
        return $this;
    }

    public function setAmount($amount) {
        $this->_cfg_cpm_amount = $amount;
        return $this;
    }

    public function setCustom($custom) {
        $this->_cfg_cpm_custom = $custom;
        return $this;
    }

    public function setTransDate($date) {
        if ($this->IsDate($date)) {
            $date = new DateTime($date);
            $this->_cfg_cpm_trans_date = $date->format('YmdHis');
            return $this;
            /* $date = date_create('2000-01-01');
              echo date_format($date, 'YmdHis'); */
        }
        throw new Exception("Method [setTransDate] need a good Date");
    }

    public function getPayDataArray() {
        return $dataArray = array(
            'apikey' => $this->_cfg_apikey,
            'cpm_site_id' => $this->_cfg_cpm_site_id,
            'cpm_currency' => $this->_cfg_cpm_currency,
            'cpm_payment_config' => $this->_cfg_cpm_payment_config,
            'cpm_page_action' => $this->_cfg_cpm_page_action,
            'cpm_version' => $this->_cfg_cpm_version,
            'cpm_language' => $this->_cfg_cpm_language,
            'cpm_trans_date' => $this->_cfg_cpm_trans_date,
            'cpm_trans_id' => $this->_cfg_cpm_trans_id,
            'cpm_designation' => $this->_cfg_cpm_designation,
            'cpm_amount' => $this->_cfg_cpm_amount,
            'cpm_custom' => $this->_cfg_cpm_custom
        );
    }

    private function getPaySignatureArray() {
        return $dataArray = array(
            'apikey' => $this->_cfg_apikey,
            'cpm_site_id' => $this->_cfg_cpm_site_id,
            'cpm_currency' => $this->_cfg_cpm_currency,
            'cpm_payment_config' => $this->_cfg_cpm_payment_config,
            'cpm_page_action' => $this->_cfg_cpm_page_action,
            'cpm_version' => $this->_cfg_cpm_version,
            'cpm_language' => $this->_cfg_cpm_language,
            'cpm_trans_date' => $this->_cfg_cpm_trans_date,
            'cpm_trans_id' => $this->_cfg_cpm_trans_id,
            'cpm_designation' => $this->_cfg_cpm_designation,
            'cpm_amount' => $this->_cfg_cpm_amount,
            'cpm_custom' => $this->_cfg_cpm_custom
        );
    }

    private function getPayStatusArray() {
        return $dataArray = array(
            'apikey' => $this->_cfg_apikey,
            'cpm_site_id' => $this->_cfg_cpm_site_id,
            'cpm_trans_id' => $this->_cfg_cpm_trans_id);
    }

    private function IsDate($date, $format = 'Y-m-d H:i:s') {
        $version = explode('.', phpversion());
        if (((int) $version[0] >= 5 && (int) $version[1] >= 2 && (int) $version[2] > 17)) {
            $d = DateTime::createFromFormat($format, $date);
        } else {
            $d = new DateTime(date($format, strtotime($date)));
        }
        return $d && $d->format($format) == $date;
    }

}

?>