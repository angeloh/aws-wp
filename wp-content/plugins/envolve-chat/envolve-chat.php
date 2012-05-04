<?php
/*
Plugin Name: Envolve Chat
Plugin URI: http://wordpress.org/extend/plugins/envolve-chat/
Description: Adds the Envolve real-time chat tool to your blog. Envolve is a full featured chat toolbar, similar to Facebook's chat. It lets your visitors chat with each other about your content.
Author: Envolve.com
Version: 2.3
Author URI: http://www.envolve.com
*/ 

require_once( dirname( __FILE__ ) . '/envolve_api_client.php');

/**
* Guess the wp-content and plugin urls/paths
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	  
if (!class_exists('envinc_envolve')) {
    class envinc_envolve {
        //This is where the class variables go, don't forget to use @var to tell what they're for
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'envinc_envolve_options';
        
        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "envinc_envolve";
        
        /**
        * @var string $pluginurl The path to this plugin
        */ 
        var $thispluginurl = '';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';
            
        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();
        
        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function envinc_envolve(){$this->__construct();}
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){
            //Language Setup
            $locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
            $this->thispluginurl = PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)).'/';
            $this->thispluginpath = PLUGIN_PATH . '/' . dirname(plugin_basename(__FILE__)).'/';
            
            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->getOptions();
            
            //Actions        
            add_action("admin_menu", array(&$this,"admin_menu_link"));  /* ADD AN ADMIN MENU */
			add_action('wp_head', array(&$this,"envolveCode"), 10 ,1);	/* ADD ENVOLVE TO THE HEADER OF EACH FILE */
        }
        
        /**
        * Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {
            //Don't forget to set up the default options
            if (!$theOptions = get_option($this->optionsName)) {
                $theOptions = array('default'=>'options');
				$theOptions['envinc_envolve_which_roles'] = 'everyone';
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;
            
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //There is no return here, because you should use the $this->options variable!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }
        
        /**
        * Prints envolve code
        */
        function envolveCode()
	{	
		//Check if they've accepted the powered by link on the blog
		if ( $this->options['envinc_envolve_powered_by'] && $this->options['envinc_envolve_api_key'] )
		{
			global $current_user;
			get_currentuserinfo();
		
			$doDisplay = ($this->options['envinc_envolve_which_roles'] == 'everyone') ||
				(is_user_logged_in() && (($this->options['envinc_envolve_which_roles'] == 'loggedin') ||
					 ($this->options['envinc_envolve_which_roles'] == 'administrator' && 
					($current_user->user_level == 10))));
		
			if(!$doDisplay && is_user_logged_in() && $this->options['envinc_envolve_which_roles'] == 'specific')
			{
 				$rolelist = explode(',', $this->options['envinc_envolve_specific_roles']);
                       	 	foreach($rolelist as $curRole)
                       	 	{
                       	 		foreach($current_user->roles as $uRole)
                       	 		{
                       	       		  	if(strtolower(trim($curRole)) == $uRole)
                               		        {
							$doDisplay = true;
                                        	}
                                	}
                        	}
			}

			if ( $doDisplay )
			{	
				//insert location metatag
				if(strlen($this->options['envinc_envolve_which_lang']) > 0)
				{
				echo "<meta name=\"gwt:property\" content=\"locale=" . $this->options['envinc_envolve_which_lang'] ."\">";
				}
			
				if(is_user_logged_in())
				{
					$firstName = $current_user->display_name;
					$lastName = NULL;
					$avatarURL = function_exists('bp_get_loggedin_user_avatar') ? bp_get_loggedin_user_avatar('html=false&type=full') : NULL;
					echo envapi_get_html_for_reg_user($this->options['envinc_envolve_api_key'], $firstName, $lastName, $avatarURL, $current_user->user_level == 10, NULL);
				}
				else
				{
					echo envapi_get_code_for_anon_user($this->options['envinc_envolve_api_key']);
				}
			}
		}
		else
		{
			//Otherwise, put up a thing on their blog giving them directions to activate.
			 $installImage = "<div style=\"position: fixed; right: 0; bottom: 0;\"><a href=\"https://www.envolve.com/docs/wordpress-chat-plugin.html?utm_source=WPDirectory&utm_medium=infopage&utm_campaign=infopage&install_type=wordpress\"><img src=\"https://www.envolve.com/plugins/wordpress/wordpress_not_done.png\" /></a></div>";
			 echo $installImage;
		}
        }

        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!
            add_options_page('Envolve Chat', 'Envolve Chat', 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }
        
        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
           //Then you're going to want to change options-general.php below to the name of your top-level page
           $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }
        
        /**
        * Adds settings/options page
        */
        function admin_options_page() { 
		?>
        <div class="wrap">
         <table style="margin-top: 20px; margin-bottom: 5px;"> 
                        <tr valign="middle"> 
                            <td>
								<a href="http://www.envolve.com?utm_source=WPUser&utm_medium=EnvolveLogo&utm_campaign=PluginOptionsPage&install_type=wordpress" target="_blank"><img src="http://www.envolve.com/plugins/wordpress/logo_dark.png" style="margin-bottom: 15px" /></a>
                        	</td>
					        <td>
								<div style="font-size: 18px;"> -- Facebook-style Chat for Your Website.</div>
                        	</td> 
                        </tr>
	
                    </table>
                
         <?php
            if($_POST['envinc_envolve_save']){
                if (! wp_verify_nonce($_POST['_wpnonce'], 'envinc_envolve-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 
                $this->options['envinc_envolve_api_key'] = $_POST['envinc_envolve_api_key'];   
                $this->options['envinc_envolve_powered_by'] = ($_POST['envinc_envolve_powered_by']=='on')?true:false;     
                $this->options['envinc_envolve_which_roles'] = $_POST['envinc_envolve_which_roles'];                				
				$this->options['envinc_envolve_specific_roles'] = $_POST['envinc_envolve_specific_roles'];           		                    
				$this->options['envinc_envolve_which_lang'] = $_POST['envinc_envolve_which_lang'];           		                    
				
				$this->saveAdminOptions();
                
                echo '<div class="updated"><p>Success! Your changes were sucessfully saved!</p></div>';
            }
		?>                                   
                
                <div style="margin-left: 25px; margin-right: 25px;">
					To finish installation you'll need to get an API key from Envolve.<br />
					<h3><a href="https://www.envolve.com/admin/?utm_source=WPUser&utm_medium=GetAPILink&utm_campaign=PluginOptionsPage&install_type=wordpress#!/create" target="_blank">Get your API key here.</a></h3>
                </div>
                <form method="post" id="envinc_envolve_options">
                <?php wp_nonce_field('envinc_envolve-update-options'); ?>
                    <table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 
                        <tr valign="top"> 
                            <th><b><?php _e('Your Envolve API Key:', $this->localizationDomain); ?></b></th> 
					        <td>
					        	<?php 
									echo "<input type=\"text\" style=\"width: 500px; text-align: left;\" name=\"envinc_envolve_api_key\" id=\"envinc_envolve_api_key\" value=\"" . $this->options['envinc_envolve_api_key'] . "\">";
								?>
                        	</td> 
                        </tr>
						<tr valign="top"> 
                            <th><b><?php _e('"Powered By" Acceptance:', $this->localizationDomain); ?></b></th> 
	                        <td><input type="checkbox" id="envinc_envolve_powered_by" name="envinc_envolve_powered_by" <?php echo ($this->options['envinc_envolve_powered_by']==true)?'checked="checked"':''?>>
							    Check this box to acknowledge there is a 'powered by envolve' link on Envolve. <br />WordPress requires we ask. <b><i>Envolve will not work if this is left un-checked.</b></i>
                                <br />
                                You can remove this link by upgrading to a package with white labeling.
							</td>
                        </tr>
                        <tr>
                        	<th>
                        	</th>
                        	<td>
                        		<div style="margin-top: 10px; font-weight: bold; font-size: 16px">Optional Settings</div>
                        	</td>
                        </tr>
						<tr valign="top"> 
                            <th><b><?php _e('Display Envolve to which users?', $this->localizationDomain); ?></b></th> 
	                        <td>
                                <select id="envinc_envolve_which_roles" name="envinc_envolve_which_roles" onchange="envinc_specific_roles_onchange()">
								<option name="Everyone" value="everyone" <?php echo (($this->options['envinc_envolve_which_roles'] == 'everyone') ? 'selected' : '') ?>> Everyone </option>
								<option name="Logged-in users only" value="loggedin" <?php echo (($this->options['envinc_envolve_which_roles'] == 'loggedin') ? 'selected' : '') ?>> Logged-in users only </option>
								<option name="Administrators only" value="administrator" <?php echo (($this->options['envinc_envolve_which_roles'] == 'administrator') ? 'selected' : '')?>> Administrators only </option>
								<option name="Specific Roles" value="specific" <?php echo (($this->options['envinc_envolve_which_roles'] == 'specific') ? 'selected' : '')?>> Specific Roles</option>
								</select>
							</td>
                        </tr>
			<tr> 
                            <th></th> 
	                        <td id="envinc_envolve_specific_roles_div">
                            	Please enter a comma-separated list of roles: <br />
                                <?php 
									echo "<input type=\"text\" style=\"width: 500px; text-align: left;\" name=\"envinc_envolve_specific_roles\" value=\"" . $this->options['envinc_envolve_specific_roles'] . 
										"\">";
								?><br />
				Can't figure out the correct role names? Check out our <a href="https://www.envolve.com/support/troubleshooting.html?utm_source=WPDirectory&utm_medium=infopage&utm_campaign=infopage&install_type=wordpress">support / troubleshooting docs</a>
							</td>
                        </tr>
						<tr>
							<th><b><?php _e('Display interface in which language?', $this->localizationDomain); ?></b></th> 
							<td>
								<select id="envinc_envolve_which_lang" name="envinc_envolve_which_lang">
									<?php
										$theLangs = array("en" => "English", 
											"fr" => "French",
											"pt" => "Portuguese",
											"it" => "Italian",
											"nl" => "Dutch",
											"el" => "Greek",
											"es" => "Spanish",
											"bg" => "Bulgarian",
											"no" => "Norwegian",
											"ar" => "Arabic",
											"il" => "Hebrew",
											);
											
										$curLang = $this->options['envinc_envolve_which_lang'];
										
										foreach ($theLangs as $langCode => $langName) {
											if(strcmp($curLang, $langCode) == 0) {
												$isLangSelected = "selected=true";
											} else {
												$isLangSelected = "";
											}
											echo "<option value=\"" . $langCode . "\" " . $isLangSelected . "> " . $langName . " </option>";
										}
									?>
								</select>
							</td>
						</tr>
                        <tr>
                        	<th></th> 
                            <td><input type="submit" name="envinc_envolve_save" value="Save Settings" style="padding: 5px; font-size: 200%; cursor: hand" /></td>
                        </tr>
                    </table>
                </form>
 <br />
Many more settings are available through the <a href="https://www.envolve.com/admin/?utm_source=WPUser&utm_medium=AdditionalSettings&utm_campaign=PluginOptionsPage&install_type=wordpress#!/">Envolve administrative interface.</a>
                <script type="text/javascript">
function envinc_specific_roles_onchange()
{
    document.getElementById("envinc_envolve_specific_roles_div").style.visibility = 
		document.getElementById("envinc_envolve_which_roles").options[document.getElementById("envinc_envolve_which_roles").selectedIndex].value == "specific" ? "visible" : "hidden";
}
envinc_specific_roles_onchange();
</script>

                <?php
        }
		
		
  } //End Class
} //End if class exists statement

		
//instantiate the class
if (class_exists('envinc_envolve')) {
    $envinc_envolve_var = new envinc_envolve();
}
?>
