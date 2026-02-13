# 🛒 Shop Web: PHP & MySQL 全端購物商城專案

這是一個使用 **PHP** 核心開發的全端電子商務系統。本專案透過 **MySQL** 進行資料持久化管理，並結合 **JavaScript** 與 **CSS3** 打造流暢的使用者購物體驗。

---

## 🚀 核心功能 (Key Features)

* **完整會員系統**：實作註冊與登入功能，帳密安全採用 `password_hash()`加密。
* **動態商品目錄**：即時從資料庫讀取商品資訊，包含價格、庫存狀態與分類過濾。
* **購物車邏輯管理**：利用 `$_SESSION` 或資料庫實作購物車存取，支援商品數量動態增減。
* **結帳與訂單系統**：自動計算總金額、處理庫存扣除邏輯，並產出歷史訂單紀錄。
* **非同步資料互動 (AJAX)**：部分功能透過 JavaScript實作，提升操作流暢度。

---

## 🛠️ 技術棧 (Tech Stack)

| 類別 | 使用技術 |
| :--- | :--- |
| **後端語言** | PHP 8.x |
| **資料庫** | MySQL (Relational Database) |
| **前端語法** | HTML5, CSS3 (Flexbox/Grid), JavaScript (ES6+) |
| **安全防護** | PDO/MySQLi Prepared Statements |

---

## 📊 資料庫架構 (Database Schema)

本專案採用關聯式資料庫設計，確保資料一致性（Referential Integrity）：

1.  **users**: 儲存使用者帳號、加密密碼及基本資料。
2.  **products**: 儲存商品名稱、描述、價格及剩餘庫存。
3.  **cart**: 儲存特定使用者暫存的商品與數量。
4.  **orders**: 儲存交易成功的紀錄，包含總金額與時間戳記。

---

## 🛡️ 後端安全實作 (Security Highlights)

作為後端開發專案，本系統實作了以下安全機制：
* **防止 SQL 注入 (SQL Injection)**：全站涉及資料庫的操作皆使用 **預處理陳述式 (Prepared Statements)**，不直接串接變數。
* **跨站腳本攻擊 (XSS) 防護**：所有使用者輸出的資料皆經過 `htmlspecialchars()` 轉義，防止惡意指令執行。
* **密碼安全性**：拒絕明碼儲存，強制使用雜湊函數進行密碼保護。
* **權限控管**：嚴格驗證 Session 狀態，防止未登入使用者存取後端管理或結帳頁面。

---

## 📂 專案目錄結構
index.php      # 購物商城首頁
product.php    # 商品詳細資訊頁
cart.php       # 購物車管理頁面
checkout.php   # 結帳與訂單生成
.....php       # 其他功能頁

## ⚙️ 如何在本地端運行
確保環境已安裝 XAMPP, WAMP 或任何支援 PHP/MySQL 的環境。

將本專案複製至 htdocs 資料夾下。

開啟 phpMyAdmin，建立名為 shop_db 的資料庫，並匯入專案中的 .sql 檔案。

修改資料庫中帳密設定。

在瀏覽器輸入 http://localhost/shop_web 即可開始瀏覽。
