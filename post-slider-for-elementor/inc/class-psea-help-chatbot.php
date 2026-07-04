<?php

/**
 * Smart Post Showcase's help chatbot loader.
 *
 * Just wires the shared SolverWP_Help_Chatbot library (see /chatbot, copied
 * unchanged from the nextpage-helper plugin — every SolverWP product uses the
 * same chatbot) to this product's config. Nothing to edit here — to change
 * the AI server URL, product info, allowed pages, or knowledge base, edit
 * chatbot/config.php instead.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once PSEA_ROOT_PATH . 'chatbot/solverwp-help-chatbot.php';

if ( class_exists( 'SolverWP_Help_Chatbot' ) ) {
	$psea_chatbot_config = require PSEA_ROOT_PATH . 'chatbot/config.php';
	new SolverWP_Help_Chatbot( $psea_chatbot_config );
}
