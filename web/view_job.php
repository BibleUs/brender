<script>
	$(function() {
		$("button.switchbg").button({
	        icons: {
	            primary: "ui-icon-gear"
	        }
	    });
	    
	    $("button.switchbg").click(function() {
			$(".over").toggleClass("brender-overlay", 100);
			return false;
		});
		
		// EDIT JOB dialog START
			//$('input#edit_directstart').attr('checked', true);
		var updateid = $('input#updateid'),
			project = $('input#edit_project'),
			scene = $('input#edit_scene'),
			shot = $('input#edit_shot'),
			filetype = $('select#edit_filetype'),
			config = $('select#edit_config'),
			progress_status = $('select#progress_status'),
			start = $('input#start'),
			end = $('input#edit_end'),
			chunks = $('input#edit_chunks'),
			priority = $('input#edit_priority'),
			rem = $('input#edit_rem'),
			directstart = $('input#edit_directstart').is(':checked');
		
		
		
		$("#edit_job").dialog({
			autoOpen: false,
			height: 400,
			width: 450,
			modal: true,
			resizable: false,
			buttons: {
				Cancel: function() {
					$(this).dialog("close");
				},
				"Duplicate job": function() { 							
						
						$.post("ajax/view_job.php", {
							updateid: updateid.val(),
							action: 'duplicate',
							project: project.val(), 
							scene: scene.val(), 
							shot: shot.val(), 
							filetype: filetype.val(), 
							config: config.val(), 
							start: start.val(), 
							end: end.val(), 
							chunks: chunks.val(), 
							priority: priority.val(), 
							rem: rem.val(), 
							directstart: directstart
						}, function(data) {
							var obj = jQuery.parseJSON(data);
							//alert(data);
							if(obj.status == true) {
								$("#edit_job").dialog("close" );
								//alert(obj.query);
								window.location= 'index.php?view=jobs';
							} else {
								alert(obj.msg);
							}
						}, "Json");				
						return false;					
				},
				"Update job": function() { 												
						$.post("ajax/view_job.php", {
							updateid: updateid.val(),
							action: 'update',
							project: project.val(), 
							scene: scene.val(), 
							shot: shot.val(), 
							filetype: filetype.val(), 
							config: config.val(), 
							start: start.val(), 
							end: end.val(), 
							chunks: chunks.val(), 
							priority: priority.val(), 
							rem: rem.val(), 
							directstart: directstart
						}, function(data) {
							var obj = jQuery.parseJSON(data);
							//alert(data);
							if(obj.status == true) {
								$("#edit_job").dialog("close" );
								alert(obj.msg);
								window.location= 'index.php?view=jobs';
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
		
		$("#edit_job_button").click(function() {
			$("#edit_job").dialog("open");
		});
		// EDIT JOB dialog END
	});
	
	
</script>

<?php
#--------read---------
	$id=$_GET[id];
	$query="select * from jobs where id='$id'";
	$results=mysql_query($query);
	$row=mysql_fetch_object($results);
		$project=$row->project;
		$id=$row->id;
		$scene=$row->scene;
		$shot=$row->shot;
		$jobtype=$row->jobtype;
		$config=$row->config;
		$output=$row->output;
		$start=$row->start;
		$end=$row->end;
		$current=$row->current;
		$chunks=$row->chunks;
		$rem=$row->rem;
		$filetype=$row->filetype;
		$progress_status=$row->progress_status;
		$progress_remark=$row->progress_remark;
		$last_edited_by=$row->last_edited_by;
		$lastseen=$row->lastseen;
		$status=$row->status;
		$priority=$row->priority;
		$total=$end-$start;
	#-------------------
	print "<h2>// job $id : $scene/<b>$shot</b> </h2>";	
		
		print "project: $project $total frames ($start-$end by $chunks)";
		$total_rendered=count_rendered_frames($id);
		print "$total_rendered rendered frames last changes made by  :: $last_edited_by $lastseen";

		
	print "<table border=0 class=\"thumbnails_table\">";
	print "<tr>";
	#-------------------------------les images ------------------------------
	$a=$start;
	$first_image=get_thumbnail_image($id,$start);

	$img_chunks=round(($total)/20);
	# print "a= $a --- start $start -- end $end -- totalframes $total img_chunks =$img_chunks </br>";
	print "<td><a href=\"index.php?view=view_image&job_id=$id&frame=$a\">$first_image</a><br/>$a<br/></td>";
	$rows=1;
	while ($a++<($total+$start)){
		$b++;
		# print " a= $a ---- b=$b/$img_chunks <br/>";
		if ($b==$img_chunks) {
			/*if ($_GET[renderpreview]) {
					$render_order="-b \'/brender/blend/$file\' -o \'/brender/render/$project/$name/$output\' -P conf/$config.py -F JPEG -f $a";
                                        # ---------------------------------
                                        print "job_render for $client :\n $render_order\n-----------\n";
                                        #send_order("any","render",$render_order,"20");
			}
			*/
                        #$thumbnail_image="../thumbnails/$project/$scene/$shot/small_$shot".str_pad($a,4,0,STR_PAD_LEFT).".$ext";
			$thumbnail_image=get_thumbnail_image($id,$a);

			print "<td bgcolor=\"$tdcolor\">";
				print "<a href=\"index.php?view=view_image&job_id=$id&frame=$a\">$thumbnail_image</a><p>$a</p>";
			print "</td>";
			$b=0;
			#  print "row = $rows";
			if ($rows++>3) {
				$rows=0;
				print "</tr><tr>";
			}
		}
	}
	print "</tr></table>";

// Update job form

		if ($filetype=="TGA"){
			$select_tga="selected";
		}
		else if ($filetype=="OPEN_EXR"){
			$select_exr="selected";
		}
		else if ($filetype=="PNG"){
			$select_png="selected";
		}
		?>
		
		<div id="edit_job">
			type	<select id="edit_filetype" name="filetype">
	                 	<option value="JPEG">JPEG</option>
	                    <option value="PNG" <?php print($select_png); ?>>PNG</option>
						<option value="TGA" <?php print($select_tga); ?>>TGA</option>
						<option value="OPEN_EXR" <?php print($select_exr); ?>>OPEN_EXR</option>
                	</select>
				config
        			<select id="edit_config" name="config">
				<?php output_config_select($config); ?>
				</select><br/>	

        		<br/>progress status<br/> 
        		<select id="progress_status" name="progress_status"> 
				<?php output_progress_status_select($progress_status); ?>
				</select> rem <input id="edit_rem" name="progress_remark" type="text" value="<?php print($progress_remark); ?>"><br /><br />
        		start:<input id="edit_start" type=text name=start size="4" value="<?php print($start); ?>">
        		end: <input id="edit_end" type="text" name="end" size="4" value="<?php print($end); ?>" />
        		chunks: <input id="edit_chunks" type="text" name="chunks" size="3" value="<?php print($chunks); ?>" />
	       		priority (1-99): <input id="edit_priority" type="text" name="priority" size="3" value="<?php print($priority); ?>" /><br />
				directstart: <input id="edit_directstart" type="checkbox" name="directstart" value="yes" /><br />
        		<input id="updateid" type="hidden" name="updateid" value="<?php print($id); ?>" />
        		<input id="edit_scene" type="hidden" name="scene" value="<?php print($scene); ?>" />
        		<input id="edit_shot" type="hidden" name="shot" value="<?php print($shot); ?>" />
        		<input type="hidden" name="view" value="jobs" />
        		<input id="edit_jobtype" type="hidden" name="jobtype" value="<?php print($jobtype); ?>" />
        		<input id="edit_project" type="hidden" name="project" value="<?php print($project); ?>" />
        		<input type="submit" name="copy" value="update job" /> or
        		<input type="submit" name="copy" value="copy job" /><br />
       		</div>




<div class="table-controls">
	<a class="btn" href="index.php?view=jobs">back to job list</a>
	<button class="switchbg btn">dark background</button>
	<a class="btn" id="edit_job_button" href="#">edit or duplicate job</a>
</div>
<div class="over"></div>
