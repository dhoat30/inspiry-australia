<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-styles.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

/* email template */
* {
  box-sizing: border-box;
  padding: 0;
  margin: 0;
  color: #0b0b0b;
}
.email-template {
  background: #efefef;
  padding: 100px 0;
}
.max-width {
  max-width: 600px;
  margin: 0 auto;
}
.padding {
  padding: 10px 20px;
}
.email-template .header {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between !important;
  align-items: center;
  background: white;
}
.email-template .header .store {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
}
.email-template .header .store img {
  width: 25px;
  cursor: pointer;
}
.email-template .header .store a {
  text-decoration: none;
  display: block;
  margin-right: 10px;
}
.email-template .header .store a:hover {
  text-decoration: underline;
  color: #222;
}
.email-template .logo img {
  width: 150px;
}

/* body */
.email-template .body {
  background: #efeae5;
  position: relative;
}
.playfair-fonts {
  font-family: "Playfair Display", serif !important;
}
.email-template .body .title {
  text-align: center;

  margin: 30px 0 10px 0;
  font-weight: 500;
  font-size: 30px;
}
.email-template .body .text-content .divider {
  position: relative;
}
.email-template .body .text-content .divider img {
  display: block;
  margin: 0 auto;
  width: 80px;
  padding: 0 20px;
  background: #efeae5;
  z-index: 100;
  position: relative;
}
.email-template .body .text-content .divider::before {
  content: "";
  width: 250px;
  height: 2px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 0;
  background-color: black;
}

.email-template .body .text-content .subtitle {
  font-size: 18px;
  font-weight: 500;
  text-align: center;
  margin-top: 20px;
}

.email-template .body .text-content .paragraph {
  font-size: 16px;
  text-align: center !important;
  font-weight: 300;
  margin-top: 10px;
}

.email-template .body .text-content .paragraph p{
  font-size: 16px;
  text-align: center !important;
  font-weight: 300;
  margin-top: 10px;
  line-height: 2.4em;
  letter-spacing: 0.05em;
}
 .paragraph{
  font-size: 16px;
  text-align: center;
  font-weight: 300;
  margin-top: 10px;
  line-height: 1.4em;
  letter-spacing: 0.05em;
  color: #303030;
}

.left-align{ 
  text-align: left !important; 
}
/* order-container */
.order-container {
  background: #efeae5;
  padding: 20px;
}
.order-content {
  background: white;
  padding: 20px;
  margin: 0 20px;
}
.order-container .text-content {
  text-align: center;
}

.order-container .text-content .meta {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
  margin-top: 20px;
  position: relative;
}
.order-container .product-cards {
  position: relative;
}
.order-container .product-cards::before {
  position: absolute;
  background: #efeae5;
  width: 100%;
  height: 2px;
  content: "";
  top: 10px;
  left: 0;
}
.order-container .product-cards::after {
  position: absolute;
  background: #efeae5;
  width: 100%;
  height: 2px;
  content: "";
  bottom: -10px;
  left: 0;
}

/* order table */
.order-container .product-cards .card table {
  width: 100%;
  text-align: left;
}
.order-container .product-cards .card table th {
  padding: 10px 0;
  color: rgb(138, 138, 138);
  font-weight: 300;
}
.order-container .product-cards .card table th:nth-child(1) {
  padding: 0 20px 0 0;
}
.order-container .product-cards .card table th:nth-child(2) {
  padding: 0 10px 0 0;
}
.order-container .product-cards .card table td:nth-child(1) {
  padding: 0 20px 0 0 !important;
}

.order-container .product-cards .card .table td {
  font-family: "Playfair Display", serif !important;
}
.order-container .product-cards .card table img {
  width: 50px !important;
  height: 40px;
  object-fit: cover;
}

.order-container .product-cards .card table .title {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-start;
  align-items: flex-start;
}
.order-container .product-cards .card table .items {
  border-spacing: 15px;
  border-collapse: separate;
  padding-bottom: 50px;
}
.order-container .product-cards .card table td span {
  display: block !important;
}
.order-container .product-cards .card table td span:nth-child(2) {
  margin-bottom: 20px;
}
.order-container .product-cards .card table td a {
  text-decoration: none !important;
}
.order-container .product-cards .card table td a:hover {
  text-decoration: underline !important;
}

@media (max-width: 500px) {
  /* order-container */
  .order-container {
    background: #efeae5;
    padding: 20px 0;
  }
  .order-content {
    background: white;
    padding: 20px 5px;
    margin: 0 10px;
  }

  .order-container .product-cards {
    padding: 10px 0 !important;
  }
  .order-container .product-cards .card table td span {
    display: block;
  }
}

/* customer contact  */
.customer-contact {
  margin: 30px 0 20px 0;
}
.customer-contact .contact-info {
  margin-top: 10px;
}
.customer-contact .contact-info span {
  display: block;
}

/* footer */
.email-template .footer {
  background: #efeae5;
  padding-bottom: 40px;
  text-align: center;
}

/* total container */
.email-template .total {
  position: relative;
}
.email-template .total tr td {
  text-align: right !important;
  margin: 0 20px !important;
}
.email-template .total table {
  border-spacing: 15px;
  margin: 10px 15px 0 auto;
}
.email-template .total::after {
  position: absolute;
  background: #efeae5;
  width: 100%;
  height: 2px;
  content: "";
  bottom: -10px;
  left: 0;
}
/* social media container */

.email-template .social-container {
  background: #efeae5;
  text-align: center;
}
.email-template .social-container .title {
  font-size: 20px;
  position: relative;
}
.email-template .social-container .title::after {
  position: absolute;
  background: #0b0b0b;
  width: 100px;
  height: 2px;
  content: "";
  bottom: -7px;
  left: 50%;
  transform: translate(-50%, 0);
}

.email-template .social-container img {
  width: 25px;
  margin: 15px 5px;
}


<?php
