<?php
    global $TS_ADVANCED_TABLESWP;
	
	if (!class_exists('TS_TablesWP_Animated_Progressbar')) {
		class TS_TablesWP_Animated_Progressbar {
			/* ----------------------- */
			/* Define Global Variables */
			/* ----------------------- */
			public $TS_TablesWP_ProgressbarIncrement;
			public $TS_TablesWP_ProgressbarAddWidth;
			public $TS_TablesWP_ProgressbarTextString;
			
			/* --------------- */
			/* Construct Class */
			/* --------------- */
            function __construct() {}

			/* ------------------------------------- */
			/* Update/Replace Progressbar Textstring */
			/* ------------------------------------- */
			function TS_TablesWP_ProgressbarNewText($string){
				$this->TS_TablesWP_ProgressbarTextString 	= $string;
			}
			
			/* ----------------------- */
			/* Add Progressbar to Page */
			/* ----------------------- */
			function TS_TablesWP_ProgressbarCreate(){
				echo '<div class="ts-settings-progressbar-container">';
					echo '<div class="ts-settings-progressbar-wrapper clearfix">';
						echo '<div class="ts-settings-progressbar-name">' . $this->TS_TablesWP_ProgressbarTextString . '</div>';
						echo '<div class="ts-settings-progressbar-bar">';
							echo '<div class="ts-settings-progressbar-value striped animated" style="width: 0%;">';
								echo '<span class="ts-settings-progressbar-tooltip">0%</span>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}

			/* ------------------------ */
			/* Calculate Next Increment */
			/* ------------------------ */
			function TS_TablesWP_ProgressbarCalculate($count){
				$this->TS_TablesWP_ProgressbarIncrement 	= 100 / $count;
			}
		
			/* ---------------------------------- */
			/* Animate Progressbar with Increment */
			/* ---------------------------------- */
			function TS_TablesWP_ProgressbarAnimate(){
				$this->TS_TablesWP_ProgressbarAddWidth 		+= $this->TS_TablesWP_ProgressbarIncrement;		
				echo '<script>
					jQuery(".ts-settings-progressbar-container .ts-settings-progressbar-name").html("' . $this->TS_TablesWP_ProgressbarTextString . '");
					jQuery(".ts-settings-progressbar-container .ts-settings-progressbar-value").stop().animate({width: "' . $this->TS_TablesWP_ProgressbarAddWidth . '%"}, "fast");
					jQuery(".ts-settings-progressbar-container .ts-settings-progressbar-tooltip").html("' . round($this->TS_TablesWP_ProgressbarAddWidth, 2) . '%");
				</script>';  
			}
		
			/* -------------------------------------- */
			/* Hide Progressbar + Preloader Animation */
			/* -------------------------------------- */
			function TS_TablesWP_ProgressbarHide($timeout) {
				echo '<script>
					setTimeout(function(){
						jQuery(".ts-preloader-animation-main").fadeOut(500);
						jQuery(".ts-settings-statistics-compile-message").fadeOut(500);						
						jQuery(".ts-settings-progressbar-container").fadeOut(500);
					}, ' . $timeout . ');
				</script>';				
			}
			
			/* ------------------ */
			/* Change Browser URL */
			/* ------------------ */
			function TS_TablesWP_ProgressbarURL($url, $timeout){
				echo '<script>
					setTimeout(function(){
						window.location="' . $url . '";
					}, ' . $timeout . ');
				</script>';		
			}
		}
	}
?>