<script>
	$(function() {
		$( "button, input:submit, a.btn").button();
	});
</script>
<?php	
	if (isset($_GET['orderby'])) {
		if ($_SESSION[orderby_client]==$_GET[orderby]) {
			$_SESSION[orderby_client]=$_GET['orderby']." desc";
		}
		else {
			$_SESSION[orderby_client]=$_GET['orderby'];
		}
	}
	if (isset($_GET['benchmark'])) {
		$benchmark=$_GET['benchmark'];
		if ($benchmark=="all") {
			print "benchmark ALL idle<br/>";
			$query="select * from clients where status='idle'";
			$results=mysql_query($query);
			while ($row=mysql_fetch_object($results)){
				$client=$row->client;
				send_order("$client","benchmark","","99");
				print "benchmark $client<br/>";
			}
		}
		else {
			print "benchmark $benchmark<br/>";
			send_order($benchmark,"benchmark","","99");
		}
		sleep(1); #...we sleep 1 sec for letting time to client to start benchmarking
	}
	if (isset($_GET['disable'])) {
		$disable=$_GET['disable'];
		if ($disable=="all") {
                        print "disable ALL<br/>";
                        $query="select * from clients where status='idle' or status='rendering'";
                        $results=mysql_query($query);
                        while ($row=mysql_fetch_object($results)){
                                $client=$row->client;
                                send_order("$client","disable","","5");
                                $msg.= "disabled $client<br/>";
                        }
        	}
        	else {
			send_order($disable,"disable","","5");
        		print "disable client : $disable<br/>";
		}
		$msg= "disabled $disable <a href=\"index.php?view=clients\">reload</a><br/>";
		sleep(1);
	}
	if (isset($_GET['enable'])) {
		$enable=$_GET['enable'];
		if ($enable=="all") {
			print "enable ALL<br/>";
			$query="select * from clients where status='disabled'";
        		$results=mysql_query($query);
			while ($row=mysql_fetch_object($results)){
				$client=$row->client;
				send_order($client,"enable","","5");
				$msg.= "enabled $client<br/>";
			}
		}
		else if ($enable=="force_all"){
			print "force enable ALL<br/>";
			$query="select * from clients";
        		$results=mysql_query($query);
			while ($row=mysql_fetch_object($results)){
				$client=$row->client;
				send_order($client,"enable","","5");
				$msg.= "enabled $client<br/>";
			}
		}
		else {
			send_order($enable,"enable","","5");
			#header( 'Location: index.php' );
		}
		sleep(2);
		$msg= "enabled $enable <a href=\"index.php?view=clients\">reload</a><br/>";
	}
	if (isset($_GET['refresh'])) {	
		checking_alive_clients();
		check_if_client_should_work();	
	}
	if (isset($_GET['delete'])) {
		$client=$_GET['delete'];
		if (!check_client_exists($client)) {
			$msg="error : client $client not found";
		}
		else {
			delete_node($client);
                	$msg="client $client deleted :: ok ";
			# print "query =$dquery";
		}
        }
	if (isset($_GET['stop'])) {
		$stop=$_GET['stop'];
		$msg= "stopped $stop <a href=\"index.php?view=clients\">reload</a><br/>";
		send_order($stop,"stop","","1");
		sleep(2);
	}
	if ($_POST['action'] == "add client") {
		$new_client_name=clean_name($_POST[new_client_name]);
		if (check_client_exists($new_client_name)) {
			$msg="<span class=\"error\">error client already exists</span>";
		}
		else if ($new_client_name == "" ) {
			$msg="<span class=\"error\">error, please enter a client name</span>";
		}
		else {
			$add_query="insert into clients values('','$new_client_name','$_POST[speed]','$_POST[machinetype]','$_POST[machine_os]','$_POST[blender_local_path]','$_POST[client_priority]','$_POST[working_hour_start]','$_POST[working_hour_end]','not running','','')";
			mysql_query($add_query);
			$msg="created new client $_POST[client] $add_query";
		}
	}

if (isset($msg)) {
	print "$msg<br/> <a href=\"index.php?view=clients\">reload</a><br/>";
}

#--------read---------
#------ listing all the clients in the table, including the ones not running------- 
	$query="select * from clients order by $_SESSION[orderby_client]";
	$results=mysql_query($query);
	?>
	<h2> // <b>clients</b> <?php output_refresh_button(); ?> </h2>
	<?php debug($query); ?>
	<table>
	<tr class=header_row>
		<td width=120><a href="index.php?view=clients&orderby=client">client name</a></td>
		<td width=32<a href="index.php?view=clients&orderby=client_priority">stats</a></td>
		<td width=120> <a href="index.php?view=clients&orderby=status">status</a></td>
		<td width=500> <a href="index.php?view=clients&orderby=rem">rem</a></td>
		<td width=200> <a href="index.php?view=clients&orderby=info">info</a></td>
		<td width=120> cmd </td>
		<td width=120><a href="index.php?view=clients&orderby=working_hour_start">workhour start</a> &nbsp; </b></td>
		<td width=120><a href="index.php?view=clients&orderby=working_hour_end">workhour end</a> &nbsp; </b></td>
		<td></td>
	</tr>
<?php 
	if (mysql_num_rows($results)==0) {
	 	echo '"<tr><td class="header_row error" colspan=8> NO clients found</td></tr>';
	}
	while ($row=mysql_fetch_object($results)){
		$client=$row->client;
		$status=$row->status;
		$rem=$row->rem;
		$info=$row->info;
		$speed=$row->speed;
		$machinetype=$row->machinetype;
		$machine_os=$row->machine_os;
		$client_priority=$row->client_priority;
		$working_hour_start=$row->working_hour_start;
		$working_hour_end=$row->working_hour_end;
		$speed=$row->speed;
		$status_class=get_css_class($status);
		if ($status<>"disabled") {
			$dis="<a href=\"index.php?view=clients&disable=$client\">disable</a>";
		}
		else if ($status=="disabled") {
			$dis="<a href=\"index.php?view=clients&enable=$client\">enable</a>";
		}
		if ($status=="not running") {
			$dis="";
			$shutdown_button="";
		}
		else {
			$shutdown_button="<a href=\"index.php?view=clients&stop=$client\"><img src=\"/web/images/icons/close.png\"></a>";
		}
		print "<tr class=$status_class>
			<td class=neutral><a href=\"index.php?view=view_client&client=$client\"><font size=3>$client</font></a> <font size=1>($machinetype)</font></td> 
			<td>$machine_os<br/><font size=1>$speed / $client_priority</font></a></td>
			<td>$status</td>
			<td>$rem</td>
			<td>$info</td>
			<td>$dis</td>
			<td>$working_hour_start</td>
			<td>$working_hour_end</td>
			<td>$shutdown_button</td>
		</tr>";
	}
	print "</table>";
?>
<div class="table-controls">
	<a class="btn" href="index.php?view=clients&benchmark=all">benchmark ALL</a> 
	<a class="btn" href="index.php?view=clients&enable=all">enable ALL</a>
	<a class="btn" href="index.php?view=clients&disable=all">disable ALL</a>
	<a class="btn" href="index.php?view=clients&refresh=1">refresh</a> 
	<a class="btn" href="index.php?view=clients&enable=force_all">force_all_enable</a>
	<a id="new_client" class="btn" href="#">add new client</a>
</div>


<script>
		$(function() {
			var name = $('input#name'),
				machine_os = $('select#machine_os :selected'),
				blender_local_path = $('input#blender_local_path'),
				machine_type = $('select#machine_type :selected'),
				speed = $('input#speed'),
				working_hour_start = $('input#working_hour_start'),
				working_hour_end = $('input#working_hour_end'),
				client_priority = $('input#client_priority');
		
			
			$("#new_client_form").dialog({
				autoOpen: false,
				width: 540,
				modal: true,
				buttons: {
					Cancel: function() {
						$(this).dialog("close");
					},
					"Add new client": function() { 							
							
							$.post("ajax/clients.php", {
								name: name.val(), 
								blender_local_path: blender_local_path.val(),
								machine_os: machine_os.val(),
								machine_type: machine_type.val(),
								speed: speed.val(),
								working_hour_start: working_hour_start.val(),
								working_hour_end: working_hour_end.val(),
								client_priority: client_priority.val(),
								action: "add_client"
							}, function(data) {
								var obj = jQuery.parseJSON(data);
								//alert(data);
								if(obj.status == true) {
									$("#dialog-form").dialog("close" );
									//alert(obj.query);
									alert(obj.msg);
									window.location= 'index.php?view=clients';
								} else {
									alert(obj.msg);
								}
							}, "Json");				
			    			return false;					
					}
				},
				close: function() {
					//allFields.val( "" ).removeClass( "ui-state-error" );
				}
			});
			
			$("#new_client")
			.click(function() {
				$( "#new_client_form" ).dialog( "open" );
			});

	
		});
</script>


<?php show_new_client_form(); ?>

<?php function show_new_client_form() { ?>
	<div id="new_client_form" title="// add new client">
		<form action="index.php" method="post">
			<input type="hidden" name="view" value="clients">
			name <input id="name" type="text" name="new_client_name" size="20"> (must be unique)<br>
			<h3>machine description</h3>
			operating system <select id="machine_os" name="machine_os">
				<option>linux</option>
				<option>mac</option>
				<option>windows</option>
			</select><br/>
			blender local path (leave empty to use the /blender remote folder in brender_root) : <br/><input id="blender_local_path" type="text" name="blender_local_path" size="60"><br>
			machine type <select id="machine_type" name="machinetype">
				<option>rendernode</option>
				<option>workstation</option>
			</select><br/>
			speed (number of processors (multiplicator for number of chunks)) <input id="speed" type="text" name="speed" size="2" value="2"><br>
			<h3>working hours / priority</h3>
			working hours are hours during which the workstation will be disabled<br/>
			 Start: <input id="working_hour_start" type="text" name="working_hour_start" size="10" value="07:00:00"><br/>
			 End: <input id="working_hour_end" type="text" name="working_hour_end" size="10" value="19:00:00"><br>
			 client priority (1-100) (will only render jobs with priority higher than this value)<input id="client_priority" type="text" name="client_priority" size="3" value="1"><br>
	
		</form>
	</div>
<?php } ?>


