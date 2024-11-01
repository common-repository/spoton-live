<?php
/**
 * Plugin Name: SpotOn Live
 * Plugin URI: http://spotonmarketing.dk
 * Description: This plugin creates a connection between WordPress and SpotOn Live
 * Version: 1.2.2
 * Author: SpotOn Marketing
 * License: GPL2
 */

include_once("modules/Core/Core.php");
include_once("modules/UTMTracking/UTMTracking.php");
include_once("modules/CallTracking/CallTracking.php");
include_once("modules/ContactForm7/ContactForm7.php");
include_once("modules/GravityForms/GravityForms.php");
include_once("modules/Hooks/Hooks.php");

// Core
$core = new Module_Core();
$core->setVersion('1.2.2');
$core->attach();

// UTM
$utmTracking = new Module_UTMTracking();
$utmTracking->attach();

// Call tracking
$callTracking = new Module_CallTracking();
$callTracking->attach();

// Gravity forms
$gravityForms = new Module_GravityForms();
$gravityForms->attach();

// ContactForm7
$contactForm7 = new Module_ContactForm7();
$contactForm7->attach();

// Hooks
$hooks = new Module_Hooks();
$hooks->attach();