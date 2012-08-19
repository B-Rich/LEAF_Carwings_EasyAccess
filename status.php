<?php
require_once("functions.inc.php");

$needs_footer = FALSE;
if (!$has_header) {
	_header('LEAF Status');
	$needs_footer = TRUE;
}

if (empty($using_query)) {
	$result = execute_command(CAR_UPDATE);
	if ($result !== 'true') {
		die("Status Update failed: $result\n");
	}
	$using_query = CAR_UPDATE_QUERY;
}

$result = execute_command($using_query);
$json = json_decode($result);
if ($json === FALSE) {
	die("Query result failed to parse as JSON: $result\n");
} else {
	?>
	
	Battery: 
		<?php echo "$json->currentBattery/12 (" . number_format($json->currentBattery/12*100, 0) . "%)" ?><br/>
	<hr/>

	Plugged in: 
		<?php echo ($json->currentPluggedIn == 1 ? '<span class="on">YES</span>' : '<span class="off">NO</span>') ?><br/>
	Charging: 
		<?php echo ($json->currentCharging == 'NOT_CHARGING' ? '<span class="off">NO</span>' : '<span class="on">YES</span>') ?><br/>
	Charge Time:
		<?php echo $json->chargeTime ?> (trickle)<br/>
		<?php if (TRUE || $json->chargeTime220): ?>
			<span class="charge_220">(<?php echo $json->chargeTime220 ?> w/fast charge)</span><br/>
		<?php endif; ?>
	<hr/>
	
	Climate control:
		<?php echo ($json->currentHvac ? '<span class="on">ON</span>' : '<span class="off">OFF</span>') ?><br/>
	<hr/>

	Range: 
		<?php
		if (strtoupper($car->country) == 'US') {
			$units = 'miles';
			$range['HvacOff'] = number_format($json->rangeHvacOff*0.000621371192, 0);
			$range['HvacOn'] = number_format($json->rangeHvacOn*0.000621371192, 0);
		} else {
			$units = 'km';
			$range['HvacOff'] = number_format($json->rangeHvacOff/1000, 0);
			$range['HvacOn'] = number_format($json->rangeHvacOn/1000, 0);
		}
		?>
		<?php echo $range['HvacOff'] . ' ' . $units ?><br/>
		<span class="range_wclimate">(-<?php echo ($range['HvacOff']-$range['HvacOn']) . ' ' . $units ?> w/climate control)<br/>
	<hr/>

    <br/>
    <?php if (!isset($_GET['book'])): ?>
        <a href="#" onclick="window.location.href='/status.php?book=y&amp;id=<?php echo $_GET['id'] ?>';return false;">Bookmark this page</a>
    <?php endif; ?>

	<?php
	/*
	echo "<pre>";
	var_dump($json);
	echo "</pre>";
	*/
}

if ($needs_footer) {
	footer();
}
