<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CinetPayTransaction
 *
 * @author Istat Toukea
 */
class CinetPayTransaction {

    const STATE_PAYMENT_CONFIRMED = '00', STATE_PAYMENT_WAITING = '623';

    var $cpm_site_id,
            $signature,
            $cpm_amount,
            $cpm_trans_id,
            $cpm_custom,
            $cpm_currency,
            $cpm_payid,
            $cpm_payment_date,
            $cpm_payment_time,
            $cpm_error_message,
            $payment_method,
            $cpm_phone_prefixe,
            $cel_phone_num,
            $cpm_ipn_ack,
            $created_at,
            $updated_at,
            $cpm_result,
            $cpm_trans_status,
            $cpm_designation,
            $buyer_name;

    public function CinetPayTransaction($array = null) {
        if ($array != null) {
            $this->build($array);
        }
    }

    public function isConfirmed() {
        return $this->cpm_trans_status == self::STATE_PAYMENT_CONFIRMED;
    }

    public function isWaiting() {
        return $this->cpm_trans_status == self::STATE_PAYMENT_WAITING;
    }

    public function isAccepted() {
        return $this->isConfirmed() || $this->isWaiting();
    }

    //put your code here
    public function build($array) {
        ToolKits::remount($this, $array);
        return $this;
    }

    public function toString($add_default_parent_tag = true, $public_property_only = false) {
        $array = $this->toArray($public_property_only);
        $string = "";
        foreach ($array as $key => $value) {
            try {
                if ($value instanceof DbEntity) {
                    $string.="<$key>" . $value->toString(false) . "</$key>";
                } else {
                    $string.="<$key>" . $value . "</$key>";
                }
            } catch (Exception $e) {
                
            }
        }
        if ($add_default_parent_tag) {
            $class_name = get_class($this);
            $string = "<$class_name>" . $string . "</$class_name>";
        }
        return $string;
    }

    public function toArray($public_property_only = false) {
        $array = ToolKits::dismount($this, $public_property_only);
        return $array;
    }

//put your code here
}
