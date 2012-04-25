<?php session_start();

/**
 * Launch Template
 *
 * Loads the launch.php include for the Launch page
 *
 * @package WordPress
 * @subpackage Launch_Effect
 * 
 */

get_header(); 

// STORE REFERRED BY CODE
$_SESSION['referredBy'] = $referralindex;

// Template Name: Launch Module

// LOG VISITS AND CONVERSIONS
logVisits($referralindex, $stats_table);

// GET THE LAUNCH PAGE
include('inc/launch.php'); 

get_footer(); 

?>