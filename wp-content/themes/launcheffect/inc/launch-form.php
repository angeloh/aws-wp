<?php
/**
 * Launch Form
 *
 * @package WordPress
 * @subpackage Launch_Effect
 *
 */
?>
					<!-- STORE STUFF FOR JS USE -->
					
					<input type="hidden" id="blogURL" value="<?php bloginfo('url'); ?>" />
					<input type="hidden" id="twitterMessage" value="<?php if(get_option('lefx_twitter_message') !== '') { le('lefx_twitter_message'); } else { le('heading_content'); } ?>" />
					<input type="hidden" id="templateURL" value="<?php bloginfo('template_url'); ?>" />		
					
					<!-- FORM (PRE-SIGNUP) -->
					<form id="form" action="" class="signup-right">
						<fieldset>
							<input type="hidden" name="code" id="code" value="<?php codeCheck(); ?>" />
							
							<ul id="form-layout">
								<li class="first">
									<?php if(ler('label_content')) { ?>
									<label for="email">
										<?php le('label_content'); ?> 
										<?php if(ler('lefx_req_indicator')) { echo '<span> *</span>';} ?>
										<a href="#" id="reusertip">
											Returning User?
											<div id="reuserbubble">
												Simply enter your email address and submit the form to view your stats.
												<div class="reuserbubble-arrow-border"></div>
												<div class="reuserbubble-arrow"></div>
											</div>
										</a>		
									</label>
									<?php } ?>
									<input type="text" id="email" name="email" />
									
									<?php if(!get_option('lefx_cust_field1')) { ?>	
									<!-- SUBMIT BUTTON -->
									<span id="submit-button-border"><input type="submit" name="submit" value="<?php if(ler('label_submitbutton')) { le('label_submitbutton'); } else { echo 'GO'; } ?>" id="submit-button" /></span>
									<input type="image" id="submit-button-loader" src="<?php bloginfo('template_url'); ?>/im/ajax-loader.gif" />	
									
									<!-- PRIVACY POLICY LINK -->
									<?php if(get_option('lefx_enable_privacy_policy') == true) { ?>
									<span class="privacy-policy">
										<?php le('lefx_privacy_policy_label');?> <a href="#" id="modal-privacy" class="modal-trigger"><?php le('lefx_privacy_policy_heading'); ?></a>.
									</span>	
									<?php } ?>		
									<?php } ?>
									
									<!-- ERROR MESSAGING -->
									<div class="clear"></div>
									<div id="error"></div>
								</li>
								
								<!-- CUSTOM FIELDS (Premium) -->
								<?php if(lefx_version() == 'premium') { include(TEMPLATEPATH . '/premium/custom-fields.php'); } ?>				
											
								<li class="last">		
									
									<?php if(get_option('lefx_cust_field1')) { ?>						
									<!-- SUBMIT BUTTON -->
									<span id="submit-button-border"><input type="submit" name="submit" value="<?php if(ler('label_submitbutton')) { le('label_submitbutton'); } else { echo 'GO'; } ?>" id="submit-button" /></span>
									<input type="image" id="submit-button-loader" src="<?php bloginfo('template_url'); ?>/im/ajax-loader.gif" />
									
									<!-- PRIVACY POLICY LINK -->
									<?php if(get_option('lefx_enable_privacy_policy') == true) { ?>
									<span class="privacy-policy">
										<?php le('lefx_privacy_policy_label');?> <a href="#" id="modal-privacy" class="modal-trigger"><?php le('lefx_privacy_policy_heading'); ?></a>.
									</span>	
									<?php } ?>	
									<?php } ?>
								</li>
							</ul>
	
						</fieldset>
					</form>
					
					<!-- FORM (POST-SIGNUP) -->
					<form id="success" action="">
						<fieldset>			
									
							<div class="signup-left<?php if(get_option('disable_social_media') == 'true') { echo ' disable'; } ?>">
								
								<?php if(ler('label_social')) { ?>
								<h2 class="social-heading"><?php le('label_social'); ?></h2>
								<?php } ?>
								
								<div class="social-container">
									<div class="social">
										<div id="tweetblock" <?php if(get_option('lefx_disable_twitter') == 'true') { echo 'class="disable"'; } ?>></div>	
										<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1" type="text/javascript"></script>
										<div id="fblikeblock" <?php if(get_option('lefx_disable_facebook') == 'true') { echo 'class="disable"'; } ?>></div>
										<div id="plusoneblock" <?php if(get_option('lefx_disable_plusone') == 'true') { echo 'class="disable"'; } ?>></div>
										<script type="text/javascript">
											var tumblr_link_name = "<?php le('page_title'); ?>";
											var tumblr_link_description = "<?php le('bkt_metadesc'); ?>";
										</script>
										<div id="tumblrblock" <?php if(get_option('lefx_disable_tumblr') == 'true') { echo 'class="disable"'; } ?>></div>
										<div id="linkinblock" <?php if(get_option('lefx_disable_linkedin') == 'true') { echo 'class="disable"'; } ?>></div>
									</div>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>
							
							<div class="signup-right<?php if(get_option('disable_unique_link') == 'true') { echo ' disable'; } ?>">
								<?php if(ler('label_success_content')) { ?>
								<label for="email"><?php le('label_success_content'); ?></label>
								<?php } ?>
								<input type="text" id="successcode" value="" onclick="SelectAll('successcode');"/>	
							</div>
							
							<div class="clear"></div>
							<div id="pass_thru_error"></div>
						
						</fieldset>
					</form>
					
					
					<!-- FORM (RETURNING USER) -->
	
					<form id="returning" action="">
						<fieldset>
							<h2><?php le('returning_subheading'); ?></h2>
							<p><?php le('returning_text'); ?> <span class="user"> </span>.<br />
					
							<span <?php if(get_option('disable_unique_link') == 'true') { echo ' class="disable"'; } ?>>
								<span class="clicks"> </span> <?php le('returning_clicks'); ?><br />
							</span>
							
							<span <?php if(get_option('disable_unique_link') == 'true') { echo ' class="disable"'; } ?>>
								<span class="conversions"> </span> <?php le('returning_signups'); ?>
							</span><br /><br /></p>
					
							<div class="signup-left<?php if(get_option('disable_social_media') == 'true') { echo ' disable'; } ?>">
								<?php if(ler('label_social')) { ?>
								<h2 class="social-heading"><?php le('label_social'); ?></h2>
								<?php } ?>
								<div class="social-container">
									<div class="social">
										<div id="tweetblock-return" <?php if(get_option('lefx_disable_twitter') == 'true') { echo 'class="disable"'; } ?>></div>
										<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1" type="text/javascript"></script>
										<div id="fblikeblock-return" <?php if(get_option('lefx_disable_facebook') =='true') { echo 'class="disable"'; } ?>></div>
										<div id="plusoneblock-return" <?php if(get_option('lefx_disable_plusone') == 'true') { echo 'class="disable"'; } ?>></div>
										<script type="text/javascript">
											var tumblr_link_name = "<?php le('page_title'); ?>";
											var tumblr_link_description = "<?php le('bkt_metadesc'); ?>";
										</script>
										<div id="tumblrblock-return" <?php if(get_option('lefx_disable_tumblr') == 'true') { echo 'class="disable"'; } ?>></div>
										<div id="linkinblock-return" <?php if(get_option('lefx_disable_linkedin') == 'true') { echo 'class="disable"'; } ?>></div>
									</div>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>
						
							<div class="signup-right<?php if(get_option('disable_unique_link') == 'true') { echo ' disable'; } ?>">
								<?php if(ler('label_success_content')) { ?><label><?php le('label_success_content'); ?></label><?php } ?>
								<input type="text" id="returningcode" value="" onclick="SelectAll('returningcode');"/>
							</div>	
						</fieldset>
					</form>