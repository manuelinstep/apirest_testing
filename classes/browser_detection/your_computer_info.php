<?php
require_once(dirname(__FILE__)."/../../core.lib.php");

$html='';

ob_start();
?>
			<?php
			$os = '';
			$os_starter = '<h4 class="right-bar">Operating System:</h4><p class="right-bar">';
			$os_finish = '</p>';
			$full = '';
			$handheld = '';

			// change this to match your include path/and file name you give the script
			include_once('browser_detection.php');
			$browser_info = browser_detection('full');

			// $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number
			if ( $browser_info[8] == 'mobile' )
			{
				$handheld = '<h4 class="right-bar">Handheld Device:</h4>';
				if ( $browser_info[13][0] )
				{
					$handheld .= '<p class="right-bar">Device Type: ' . $browser_info[13][0] . '</p>';
				}
				if ( $browser_info[13][2] )
				{
					$handheld .= '<p class="right-bar">Mobile OS: ' . $browser_info[13][2] . ' ' .  $browser_info[13][3] . '</p>';
					if ( !$browser_info[5] )
					{
						$os_starter = '';
						$os_finish = '';
					}
				}
				if ( $browser_info[13][1] )
				{
					$handheld .= '<p class="right-bar">Mobile Browser: ' . $browser_info[13][1] . ' ' .  $browser_info[13][2] . '</p>';
				}
				if ( $browser_info[13][5] )
				{
					$handheld .= '<p class="right-bar">Mobile Server: ' . $browser_info[13][5] . ' ' .  $browser_info[13][6] . '</p>';
				}
			}

			switch ($browser_info[5])
			{
				case 'win':
					$os .= 'Windows ';
					break;
				case 'nt':
					$os .= 'Windows<br />NT ';
					break;
				case 'lin':
					$os .= 'Linux<br /> ';
					break;
				case 'mac':
					$os .= 'Mac ';
					break;
				case 'iphone':
					$os .= 'Mac ';
					break;
				case 'unix':
					$os .= 'Unix<br />Version: ';
					break;
				default:
					$os .= $browser_info[5];
			}

			if ( $browser_info[5] == 'nt' )
			{
				if ($browser_info[6] == 5)
				{
					$os .= '5.0 (Windows 2000)';
				}
				elseif ($browser_info[6] == 5.1)
				{
					$os .= '5.1 (Windows XP)';
				}
				elseif ($browser_info[6] == 5.2)
				{
					$os .= '5.2 (Windows XP x64 Edition or Windows Server 2003)';
				}
				elseif ($browser_info[6] == 6.0)
				{
					$os .= '6.0 (Windows Vista)';
				}
				elseif ($browser_info[6] == 6.1)
            {
               $os .= '6.1 (Windows 7)';
            }
            elseif ($browser_info[6] == 'ce')
            {
               $os .= 'CE';
            }
			}
			elseif ( $browser_info[5] == 'iphone' )
			{
				$os .=  'OS X (iPhone)';
			}
			elseif ( ( $browser_info[5] == 'mac' ) &&  ( $browser_info[6] >= 10 ) )
			{
				$os .=  'OS X';
			}
			elseif ( $browser_info[5] == 'lin' )
			{
				$os .= ( $browser_info[6] != '' ) ? 'Distro: ' . ucfirst ($browser_info[6] ) : 'Smart Move!!!';
			}
			elseif ( $browser_info[5] && $browser_info[6] == '' )
			{
				$os .=  ' (version unknown)';
			}
			elseif ( $browser_info[5] )
			{
				$os .=  strtoupper( $browser_info[5] );
			}
			$os = $os_starter . $os . $os_finish;
			$full .= $handheld . $os . '<h4 class="right-bar">Current Browser / UA:</h4><p class="right-bar">';
			if ($browser_info[0] == 'moz' )
			{
				$a_temp = $browser_info[10];// use the second to last item in array, the moz array
				$full .= ($a_temp[0] != 'mozilla') ? 'Mozilla/ ' . ucfirst($a_temp[0]) . ' ' : ucfirst($a_temp[0]) . ' ';
				$full .= $a_temp[1] . '<br />';
				$full .= 'ProductSub: ';
				$full .= ( $a_temp[4] != '' ) ? $a_temp[4] . '<br />' : 'Not Available<br />';
				$full .= ($a_temp[0] != 'galeon') ? 'Engine: Gecko RV: ' . $a_temp[3] : '';
			}
			elseif ($browser_info[0] == 'ns' )
			{
				$full .= 'Browser: Netscape<br />';
				$full .= 'Full Version Info: ' . $browser_info[1];
			}
			elseif ( $browser_info[0] == 'webkit' )
			{
				$a_temp = $browser_info[11];// use the last item in array, the webkit array
				$full .= 'User Agent: ';
				$full .= ucwords($a_temp[0]) . ' ' . $a_temp[1];
				$full .= '<br />Engine: AppleWebKit v: ';
				$full .= ( $browser_info[1] ) ? $browser_info[1] : 'Not Available';
			}
			else
			{
				$full .= ($browser_info[0] == 'ie') ? strtoupper($browser_info[7]) : ucwords($browser_info[7]);
				$full .= ' ' . $browser_info[1];
			}
			echo $full . '</p>';
			?>
	</div>
<?php
$html=ob_get_contents();
ob_end_clean();
?>
