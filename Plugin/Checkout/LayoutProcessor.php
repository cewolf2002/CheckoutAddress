<?php
namespace Cewolf\CheckoutAddress\Plugin\Checkout;

class LayoutProcessor
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'])) {
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as &$payment) {
                if (isset($payment['children']['form-fields']['children'])) {
                    $fields = &$payment['children']['form-fields']['children'];
                    // 調整欄位排序（依據指定順序）
                    if (isset($fields['firstname']))   $fields['firstname']['sortOrder']   = 10; // 姓
                    if (isset($fields['lastname']))    $fields['lastname']['sortOrder']    = 20; // 名
                    if (isset($fields['telephone']))   $fields['telephone']['sortOrder']   = 30; // 電話號碼
                    if (isset($fields['company']))     $fields['company']['sortOrder']     = 40; // 公司
                    if (isset($fields['vat_id']))      $fields['vat_id']['sortOrder']      = 50; // 統一編號
                    if (isset($fields['postcode']))    $fields['postcode']['sortOrder']    = 60; // 郵遞區號
                    if (isset($fields['city']))        $fields['city']['sortOrder']        = 70; // 縣市
                    if (isset($fields['street']))      $fields['street']['sortOrder']      = 80; // 路名
                    if (isset($fields['country_id']))  $fields['country_id']['sortOrder']  = 90; // 國家
                }
            }
        }
        return $jsLayout;
    }
}
