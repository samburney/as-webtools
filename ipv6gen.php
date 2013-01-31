<html>
	<style type="text/css">
		.note {
			font-size: 9pt;
		}
	</style>
	<h2>Air-Stream 6to4 Subnet Generator</h2>
	<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
		<p>
			Host IPv4 Address: <input type="text" name="ip_address"<? if($_REQUEST['ip_address']){ ?> value="<?= $_REQUEST['ip_address'] ?>"<? } ?>) /><br />
			6to4 Prefix: <input type="text" name="prefix" value="<?= isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : 'fedc' ?>" /> <span class="note">Note: fedc is the default for Air-Stream, use 2002 for Internet 6to4</span><br />
			Host Subnet:
			<select name="subnet">
<? for ($i=24; $i <= 32 ; $i++) { ?>
				<option value="<?= $i ?>"<? if(($i == 27 && !isset($_REQUEST['subnet'])) || $i == $_REQUEST['subnet']){ ?> selected="selected"<? } ?>>/<?= $i ?></option>
<? } ?>
			</select>
		</p>
		<p>
			<input type="submit" name="submit" value="Submit" /><br />
		</p>
	</form>
<?php
if($_REQUEST['submit']){
	$prefix = $_REQUEST['prefix'];
	$ip_address = $_REQUEST['ip_address'];
	$subnet = $_REQUEST['subnet'];

	if($ip_long = ip2long($ip_address)){
		$dec_octets = explode(".", $ip_address);
		$hex_octets = array();

		$host_octet = $dec_octets[3];
		
		// Determine subnet mask octet
		$cidr_octet = 32 - $subnet;
		$binmask = str_pad('', 8 - $cidr_octet, "1") . str_pad('', $cidr_octet, "0");
		$decmask = bindec($binmask);
		$net_octet = $dec_octets[3] & $decmask;

		foreach($dec_octets as $octet){
			$hex_octets[] = str_pad(dechex($octet), 2, '0', STR_PAD_LEFT);
		}
		$hex_octets[3] = str_pad(dechex($net_octet), 2, '0', STR_PAD_LEFT);

		// Trim zeroes
		$hex_octets[0] = ltrim($hex_octets[0], "0");
		$hex_octets[2] = ltrim($hex_octets[2], "0");
		if(!$hex_octets[0]){
			$hex_octets[1] = ltrim($hex_octets[1], "0") ? ltrim($hex_octets[1], "0") : "0";
		}
		if(!$hex_octets[2]){
			$hex_octets[3] = ltrim($hex_octets[3], "0");
		}

		$subnet = $prefix . ':' . $hex_octets[0] . $hex_octets[1];
		if($hex_octets[2] || $hex_octets[3]){
			if($hex_octets[2] != '00'){
				$subnet .= ':' . $hex_octets[2] . $hex_octets[3];
			}
			else{
				$subnet .= ':' . $hex_octets[3];
			}
		}
		$subnet .= '::';

?>
	<h3>Result</h3>
	<p>
		<b>Full subnet:</b> <?= $subnet ?>/48<br />
		<b>Suggested subnet:</b> <?= $subnet ?>/64<br />
		<b>Suggested host address:</b> <?= $subnet ?><?= $host_octet ?>/64<br />
	</p>
<?
		
	}
	else{
		$errors[] = "$ip_address is not a valid IPv4 address";
	}
}

if(@isset($errors)){
	foreach($errors as $error){
		echo "$error<br />";
	}
}
?>
</html>