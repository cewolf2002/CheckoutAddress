# Magento 2 Billing Address 排序自訂模組技術文件

## 模組名稱

`Cewolf_CheckoutAddress`

---

## 1. 目的 Purpose

**繁體中文：**  
本模組用於 Magento 2 結帳頁面，讓「新增帳單地址（Billing Address）」表單的欄位順序可依業務需求自訂排序。原生 Magento 僅允許 Shipping Address 透過 XML 覆寫排序，Billing Address 則須以 PHP Plugin 修改 jsLayout。本模組解決此限制，確保所有付款方式下的 Billing Address 欄位順序皆能一致、可維護。

**English:**  
This module allows customizing the field order of the “New Billing Address” form during Magento 2 checkout. Native Magento only supports reordering the Shipping Address fields via XML; however, Billing Address requires a PHP Plugin to modify the jsLayout. This module solves this limitation, ensuring consistent, maintainable ordering of Billing Address fields for all payment methods.

---

## 2. 適用範圍 Scope

- 適用於所有 Magento 2 專案（Hyvä/Luma theme 皆可用）
- 僅調整 checkout 頁面新增 Billing Address 欄位順序
- 不影響 Shipping Address 排序（如需 Shipping Address 調整，建議於主題 XML 進行）

---

## 3. 功能 Features

- 針對所有付款方式結帳時，統一 Billing Address 欄位排序
- 支援自訂欄位與順序（如需調整欄位內容，只需更改 Plugin 內排序程式）
- 不影響原有功能與資料流
- 模組化安裝，易於部署與維護

---

## 4. 技術設計 Technical Design

### 4.1 架構說明

- 利用 Magento 2 的 Plugin 機制，攔截 `Magento\Checkout\Block\Checkout\LayoutProcessor::process()` 輸出
- 遍歷所有 payment method 下的 billing address form，統一修改 form-fields 欄位的 `sortOrder`
- 完全不修改核心檔案，確保升級相容性

### 4.2 目錄結構

```text
app/code/Cewolf/CheckoutAddress/
├── etc/
│   ├── frontend/
│   │   └── di.xml
│   └── module.xml
├── Plugin/
│   └── Checkout/
│       └── LayoutProcessor.php
├── registration.php
├── composer.json
```

### 4.3 程式範例

`Plugin/Checkout/LayoutProcessor.php` 範例：

```php
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
                    // 調整欄位排序
                    if (isset($fields['firstname']))  $fields['firstname']['sortOrder']  = 10;
                    if (isset($fields['lastname']))   $fields['lastname']['sortOrder']   = 20;
                    if (isset($fields['street']))     $fields['street']['sortOrder']     = 30;
                    if (isset($fields['city']))       $fields['city']['sortOrder']       = 40;
                    if (isset($fields['postcode']))   $fields['postcode']['sortOrder']   = 50;
                    if (isset($fields['country_id'])) $fields['country_id']['sortOrder'] = 60;
                    if (isset($fields['telephone']))  $fields['telephone']['sortOrder']  = 70;
                }
            }
        }
        return $jsLayout;
    }
}
```

---

## 5. 安裝說明 Installation

### 5.1 將模組放到指定位置

將整個 `Cewolf/CheckoutAddress` 模組目錄放置於 `app/code/` 路徑下：

```bash
cp -R Cewolf/CheckoutAddress app/code/Cewolf/CheckoutAddress
```

### 5.2 啟用模組

於 Magento 根目錄執行以下指令：

啟用模組：

```bash
php bin/magento setup:upgrade
```

清除快取：

```bash
php bin/magento cache:clean
```

（選用）編譯 DI 與前端資源（建議於生產環境執行）：

```bash
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
```

### 5.3 驗證結果

- 前往前台結帳頁。
- 選擇任一付款方式 → 點選「新增帳單地址」。
- 確認欄位排序是否依照 `LayoutProcessor.php` 中的 `sortOrder` 設定。
- 若需修改欄位順序，只需調整 `LayoutProcessor.php` 中的排序程式，然後重新清除快取。
