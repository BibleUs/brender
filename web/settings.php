<?php
#print "sid = $sid <br/>";



if (isset($_GET['enable_sound'])) {
	$query="update status set sound='yes'";
	mysql_unbuffered_query($query);
	print "sound enabled<br/>";
}
if (isset($_GET['disable_sound'])) {
	$query="update status set sound='no'";
	mysql_unbuffered_query($query);
	print "sound disabled<br/>";
}
if (isset($_GET['test'])) {
}

order_status();
system_status();
theme_chooser();
print "<br/>";
print "<a class=\"button grey\" href=\"index.php?view=projects\">manage projects</a> ";
// include "projects.php";
print "<a class=\"button grey\" href=\"index.php?view=render_configs\">manage render configs</a>";
print "<h2>// <strong>session</strong> settings</h2>";
print_r($_SESSION);

#------------------ server log-----------------
function order_status() {
	$query="select * from orders order by orders";
	$results=mysql_query($query);
	print "<table border=0><tr>";
	while ($row=mysql_fetch_object($results)){
                $client=$row->client;
                $priority=$row->priority;
                $id=$row->id;
                $rem=$row->rem;
	        $orders=$row->orders;
		$status=$row->status;
		 $tdstyle="none";
		 $text="";
		 if ($orders=="render") {
		    	$tdstyle="render";
		 }
		 else if($orders=="disable" or $orders=="enable") {
		 	$text="($client $orders)";
		    	$tdstyle="disable";
		} 
		 else if($orders=="ping") {
		 	$text=$client;
		    	$tdstyle="ping";
		} 
		print "<td class=\"$tdstyle\" width=\"50\">$text</td>";
	}
	print "</tr></table>";
}

#---------------system status ---------
function system_status() {
	$query="select server,status,pid,started,timediff(now(),started) as uptime,sound from status;";
	$results=mysql_query($query);
	print "<table width=600>";
	print "<tr>
		<td bgcolor=cccccc width=120 align=center><b> &nbsp; server &nbsp; </b></td>
		<td bgcolor=cccccc width=120 height=30 align=center><b>status</b></td>
		<td bgcolor=cccccc width=120 height=30 align=center><b>pid</b></td>
		<td bgcolor=cccccc width=120 height=30 align=center><b>uptime</b></td>
		<td bgcolor=cccccc width=120 height=30 align=center><b>started</b></td>
		<td bgcolor=cccccc width=120 height=30 align=center><b>sound</b></td>
	</tr>";
	while ($row=mysql_fetch_object($results)){
		$server=$row->server;
		$status=$row->status;
		$started=$row->started;
		$uptime=$row->uptime;
		$sound=$row->sound;
		$pid=$row->pid;
		$bgcolor="#cccccc";
		print "<tr>
			<td bgcolor=$bgcolor align=center>$server</td>
			<td bgcolor=ddddcc align=center>$status</td> 
			<td bgcolor=ddddcc align=center>$pid</td> 
			<td bgcolor=ddddcc align=center>$uptime</td> 
			<td bgcolor=ddddcc align=center>$started</td> 
			<td bgcolor=ddddcc align=center>
				$sound<br/> 
				<a href=\"index.php?view=settings&enable_sound=1\">yes</a>
				<a href=\"index.php?view=settings&disable_sound=1\">no</a>
			</td>
		</tr>";
	}
	print "</table>";
}
#------------------- client status----------
function client_status() {
	$query="select * from clients order by status";
	        $results=mysql_query($query);
		print "<table border=0>";
		print"<tr>";
		while ($row=mysql_fetch_object($results)){
			$client=$row->client;
			$status=$row->status;
			if ($status<>"disabled") {
			   $bgcolor="#bcffa6";
			}
			if ($status=="disabled") {
			    $bgcolor="#ffaa99";
			}	
			if ($status=="rendering") {
			    $bgcolor="#99ccff";
			}
			if ($status=="not running") {
			      $bgcolor="#ffcc99";
			}
			print "<td class=\"tdclient\"><font color=$bgcolor>$client</font></td>";
		}
		print "</tr>";
		print "</table>";
}
# ------------------------- theme chooser --------------------
function theme_chooser() {
	print "<table width=600>";
	print " <tr>
			<td bgcolor=cccccc width=120 align=center colspan=4 height=25><b> &nbsp; theme ($_SESSION[theme]) &nbsp; </b></td>
		</tr>
		<tr>
			<td bgcolor=ddddcc align=center><b> &nbsp; <a href=\"index.php?view=settings&theme=brender\">brender</a> &nbsp; </b></td>
			<td bgcolor=ddddcc align=center><b> &nbsp; <a href=\"index.php?view=settings&theme=brender_dark\">brender_dark</a> &nbsp; </b></td>
		</tr>
	</table>
	";
}
?>
