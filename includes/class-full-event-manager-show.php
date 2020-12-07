<?php

/**
 * Fired during plugin activation
 *
 * @link:      https://jonnas1982.github.io/Full-Event-Manager/
 * @since      0.0.1
 *
 * @package    full_event_manager
 * @subpackage full_event_manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    full_event_manager
 * @subpackage full_event_manager/includes
 * @author:    jonnas1982 <contact@jonas-eriksen.dk>
 */
class FullEventManager {

	function show ()
    {
        ?>
        <style>
        .beta table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        .beta td, .beta th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        }

        .beta tr:nth-child(even) {
        background-color: #dddddd;
        }
        </style>

        <table cellpadding="3" cellspacing="0">
        <tbody style="vertical-align: top;">
        <tr>
            <td style="border:0px !important;">
            <?php
            $this->show_company_list("public");
            ?>
            </td>
		</tr>
		<tr>
            <td style="border:0px !important; min-width:450px !important; width:100%;">
            <?php
            $this->show_public_calender();
            ?>
            </td>
        </tr>
        </tbody>
        </table>
        <?php
    }

	function show_company_list($mode = "public")
    {
        if ($mode == "public") 
        {
            $list = $this->DB->get_list(date('Y-m-d', strtotime("last Monday")));
            $Tabel = array(
                        "Virksomhed" => "virksomhed",
                        "Dato fra" => "dato_fra", 
                        "Dato til" => "dato_til", 
                        "Uge" => "uge"
                    );
        }
        else
        {
            $list = $this->DB->get_list(date('Y-m-d', strtotime("last Monday")));
            $Tabel = array(
                        "Virksomhed" => "virksomhed",
                        "Dato fra" => "dato_fra", 
                        "Dato til" => "dato_til", 
                        "Uge" => "uge"
                    );
        }

        if ($mode == "public") 
        {
            ?>
            <table cellpadding="3" cellspacing="0" class="beta" style="width:100%;">
                <thead>
                    <th>Virksomhed</th>
                    <th>E-Mail</th>
                    <th>Uge</th>
                    <th></th>
                    <tr>
                        <form action="<?php echo page_url(); ?>" method="post">
                            <td><input type="text" id="virksomhed" name="virksomhed" value="" placeholder="Virksomhed" required></td>
                            <td><input type="email" id="email" name="email" value="" placeholder="E-Mail" required></td>
                            <td>
                                <select name="uge" id="uge">
                                    <?php 
                                        $counter = 0;
                                        $global_counter = 0;
                                        $dato_til = date("Y-m-d", time());
                                        $dates = array();
                                        while ($counter <= 20 && $global_counter <= 10000) 
                                        {
                                            $dato_fra = date('Y-m-d', strtotime("next monday", strtotime($dato_til)));
                                            $dato_til = date('Y-m-d', strtotime("next sunday", strtotime($dato_fra)));
                                            if ($this->DB->date_checker($dato_fra, $dato_til) == "fri") 
                                            {
                                                $date = new DateTime($dato_fra);
                                                $week = $date->format("W");
                                                $data = "uge:$week;dato_fra:$dato_fra;dato_til:$dato_til";
                                                ?> 
                                                <option value="<?php echo $data ?>"><?php echo $week; ?></option>
                                                <?php
                                                $counter++;
                                            }
                                            $global_counter++;
                                        }

                                    ?>
                                </select>
                            </td>
                            <td><input type="submit" name="pub-create-list" value="Opret"></td>
                        </form> 
                    </tr>
                </thead>
            </table>
		<?php
		}
		?>




        <table cellpadding="3" cellspacing="0" class="beta" style="width:100%;">
        <thead>
        <?php
        foreach ($Tabel as $Headline => $DB_Headline) 
        {
            echo '<th>'.$Headline.'</th>';
        }
        foreach ($list as $key => $event) 
        {
            if (strtolower($event["virksomhed"]) == "optaget" && $mode == "public") 
            {
                // Vis ikke låste datoere
            }
            else 
            {
                ?>
                <tr>
                
                <?php
                foreach ($Tabel as $Headline => $DB_Headline) 
                {
                    echo '<td>'.$event[$DB_Headline].'</td>';
                }
                ?>
                </tr>
                
                <?php
            }
		}
		
		?>
		</thead>
        </table>
        <?php
    }

	function show_public_calender()
    {
        require_once __DIR__ . "/kalender/index.php";
	}
	
	function request_handler()
	{
		if (isset($_POST["pub-create-list"])) 
		{
			if (isset($_POST["virksomhed"]) && isset($_POST["email"]) && isset($_POST["uge"])) 
			{
				$_POST["virksomhed"] = htmlspecialchars(strip_tags($_POST["virksomhed"]), ENT_QUOTES);

				$post_dato = explode(";", $_POST["uge"]);

				$post_dato2 = array();
				foreach ($post_dato as $key => $value) 
				{
					$array = explode(":",$value);
					$post_dato2[$array[0]] = $array[1];
				}
				$post_dato = $post_dato2;

				if (isset($post_dato["dato_fra"]) && isset($post_dato["dato_til"]) && isset($post_dato["uge"])) 
				{
					// Logic
					$date = new DateTime($post_dato["dato_fra"]);
					$week1 = $date->format("W");
					$date = new DateTime($post_dato["dato_til"]);
					$week2 = $date->format("W");

					$tjek2 = date('Y-m-d', strtotime("next sunday", strtotime($post_dato["dato_fra"])));

					if ($week1 == $week2 && $week1 == $post_dato["uge"] && $post_dato["dato_til"] == $tjek2 && date('N', strtotime($post_dato["dato_fra"])) === '1' && date('N', strtotime($post_dato["dato_til"])) === '7') 
					{
						$uge = $week1;
					}
					else 
					{
						$uge = "Start og slut dato er ikke samme uge";
					}

					// Send emails
					$emails = $this->DB->get_email_list();
					foreach ($emails as $key => $email) 
					{
						if ($email["email_type"] == "confirmation") 
						{
							$sender_confirmation = "DSE Aalborg"; // Predefined senders in the API file

							$subject_confirmation = $email["email_headline"]; // The subject of the email
							$message_confirmation = $email["besked"]; // The message of the email
							$receiver_confirmation = $_POST["email"]; // The email address for the email receiver

							$message_confirmation = str_replace ("[virksomhed]", $_POST["virksomhed"], $message_confirmation);
							$message_confirmation = str_replace ("[dato_fra]", $post_dato["dato_fra"], $message_confirmation);
							$message_confirmation = str_replace ("[dato_til]", $post_dato["dato_til"], $message_confirmation);
							$message_confirmation = str_replace ("[uge]", $uge, $message_confirmation);
						}
						if ($email["email_type"] == "notification") 
						{
							$sender_notification = "DSE Aalborg"; // Predefined senders in the API file

							$subject_notification = $email["email_headline"]; // The subject of the email
							$message_notification = $email["besked"]; // The message of the email
							$receiver_notification = $email["email_to"]; // The email address for the email receiver

							$message_notification = str_replace ("[virksomhed]", $_POST["virksomhed"], $message_notification);
							$message_notification = str_replace ("[dato_fra]", $post_dato["dato_fra"], $message_notification);
							$message_notification = str_replace ("[dato_til]", $post_dato["dato_til"], $message_notification);
							$message_notification = str_replace ("[uge]", $uge, $message_notification);
						}
					}

					$Mail_Sender = new Mail_Sender();
					// Send auto confirmation
					$result = $Mail_Sender->send_mail($sender_confirmation, $subject_confirmation, $message_confirmation, $receiver_confirmation); // Send the mail
					if ($result === true) 
					{
						//Mail sendt
						$auto_c = "1";
					}
					else 
					{
						$auto_c = "0";
					}
					// Send auto notification
					$result = $Mail_Sender->send_mail($sender_notification, $subject_notification, $message_notification, $receiver_notification); // Send the mail
					if ($result === true) 
					{
						//Mail sendt
						$auto_n = "1";
					}
					else 
					{
						$auto_n = "0";
					}
					$Mail_Sender = NULL;



					// Save to DB
					$dbtable = 'instragram_takeover';
					$sqlstruktur = 'virksomhed, email, dato_fra, dato_til, uge, auto_c, auto_n';
					$sqlvalues = ':virksomhed, :email, :dato_fra, :dato_til, :uge, :auto_c, :auto_n';
					$data = array(
								':virksomhed' 			=> $_POST["virksomhed"],
								':email' 				=> $_POST["email"], 
								':dato_fra'  			=> $post_dato["dato_fra"],
								':dato_til'  			=> $post_dato["dato_til"],
								':uge'      			=> $uge,
								':auto_c'      			=> $auto_c,
								':auto_n'      			=> $auto_n
							);
							
					$this->DB->add($sqlstruktur, $sqlvalues, $data, $dbtable);

					php_session_redirect_referer("?ok=" . __LINE__);
				}
				else 
				{
					php_session_redirect_referer("?error=" . __LINE__);
				}

			}
			else 
			{
				php_session_redirect_referer("?error=" . __LINE__);
			}
		}

		$this->show();
	}

}


// Add Shortcode
function vis_takeover() 
{
	//echo "Hej";
	$DSE_Instagram_takeover_WP_Show = new DSE_Instagram_takeover_WP_Show();
	
	$DSE_Instagram_takeover_WP_Show->request_handler();

	$DSE_Instagram_takeover_WP_Show = NULL;

}

add_shortcode( 'vis_takeover', 'vis_takeover' );

























































function page_url()
{
    //$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $actual_link = full_url( $_SERVER );

    if (strpos($actual_link, "studerende.dk") !== false) 
    {
        return $actual_link;
    }
}

function url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url( $s, $use_forwarded_host = false )
{
    return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}



function php_session_redirect_referer($msg = NULL)
{
	if (isset($_SESSION["HTTP_REFERER"]) && $_SESSION["HTTP_REFERER"] != "" && $_SESSION["HTTP_REFERER"] != NULL) 
	{
		header("Location: " . $_SESSION["HTTP_REFERER"] . $msg);
		?>
		<script>
		window.location.replace(<?php echo '"' . $_SESSION["HTTP_REFERER"] . '"'; ?>);
		window.history.back();
		</script>
		<?php
	}
}





// --------------------------------------------------------- //
// Funktion til at gemme kun den data man har behov for
// --------------------------------------------------------- //
// Defining the basic scraping function
function scrape_between($data, $start, $end)
{
    $data = stristr($data, $start); // Stripping all data from before $start
    $data = substr($data, strlen($start));  // Stripping $start
    $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;  // Returning the scraped data from the function
}

// --------------------------------------------------------- //
// Funktion til at gemme kun den data man har behov for
// --------------------------------------------------------- //
// Defining the basic scraping function
function scrape_to($data, $end)
{
    //$data = stristr($data, $start); // Stripping all data from before $start
    //$data = substr($data, strlen($start));  // Stripping $start
    $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;  // Returning the scraped data from the function
}

// --------------------------------------------------------- //
// Funktion til at gemme kun den data man har behov for
// --------------------------------------------------------- //
// Defining the basic scraping function
function scrape_from($data, $start)
{
    $data = stristr($data, $start); // Stripping all data from before $start
    $data = substr($data, strlen($start));  // Stripping $start
    //$stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    //$data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;  // Returning the scraped data from the function
}




/* -----------------------------------------------------------------------------
How to use

Change the service_name and service_key to match the one in the API file
You can get a service_key here https://sendmail.studerende.dk/api.php


Code use:

$sender = "DSE Aalborg"; // Predefined senders in the API file
$subject = "Some email headline/subject"; // The subject of the email
$message = "Hej,\nDeltager du til EDB møde på torsdag?\n\nHilsen dine elskede EDB koordinatorer"; // The message of the email
$receiver = "mam@studerende.dk"; // The email address for the email receiver

$Mail_Sender = new Mail_Sender();
$result = $Mail_Sender->send_mail($sender, $subject, $message, $receiver); // Send the mail
if ($result === true) 
{
	//Mail sendt
}
$Mail_Sender = NULL;

// -------------------------------------------------------------------------- */

class Mail_Sender
{
    private $service_name;
    private $service_key;
    private $alphabet;

	function __construct()
	{
        $this->service_name = "instragram-takeover";
        $this->service_key = "L4GG UBO4 IGEZ T7MU";
        $this->alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
	}
	function __destruct()
	{
		$this->service_name = "";
        $this->service_key = "";
    }
    
    function dynamic_key()
    {
        /* Base32 decoder */

		// Remove spaces from the given public key and converting to an array
		$key = str_split(str_replace(" ","",$this->service_key));
		
		$n = 0;
		$j = 0;
		$binary_key = "";

		// Decode public key's each character to base32 and save into binary chunks
        foreach($key as $char) 
        {
			$n = $n << 5;
			$n = $n + stripos($this->alphabet, $char);
			$j += 5;
		
            if($j >= 8) 
            {
				$j -= 8;
				$binary_key .= chr(($n & (0xFF << $j)) >> $j);
			}
		}
		/* End of Base32 decoder */

		// current unix time 30sec period as binary
		$binary_timestamp = pack('N*', 0) . pack('N*', floor(microtime(true)/30));
		// generate keyed hash
		$hash = hash_hmac('sha3-512', $binary_timestamp, $binary_key, true);
		
		// generate otp from hash
		$offset = ord($hash[19]) & 0xf;
		$otp = (
			((ord($hash[$offset+0]) & 0x7f) << 24 ) |
			((ord($hash[$offset+1]) & 0xff) << 16 ) |
			((ord($hash[$offset+2]) & 0xff) << 8 ) |
			(ord($hash[$offset+3]) & 0xff)
		) % pow(10, 6);
   
       //return $otp;

       $hassing_key = "Us4Bl%h\$kyqRWpZkAaj8eqnhYJiOFm^pnyX4DE5!rj8foOILoJnId%ip3Md0WW04QS%m2UwhYua8XDvdzKfNfBqTFI@vdhK*ynx";
       $hash_filler = "nY%1Y7A6Bsj@";
       
       $hash = hash_hmac('sha3-512', $otp . $this->service_key . $hash_filler, $hassing_key);

       return $hash;
    }

    function send_mail($sender, $subject, $body, $receiver)
    {
        $url = 'https://sendmail.studerende.dk/api.php';
        $myvars = 'sender=' . $sender . '&subject=' . $subject . '&body=' . $body . '&receiver=' . $receiver . '&service=' . $this->service_name . '&key=' . $this->dynamic_key();

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec( $ch );

        if ($response == "Ok") 
        {
            //Mail Sendt
            return true;
        }
    }
}