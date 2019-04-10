<?php
/*
	Templates management

	This file should not output *any* data.
*/
function templates_main()
{


}

function templates_load_by_id($id)
{

	$skipTemplates = array('header','footer');
	$templates = array();
	foreach(glob($_SERVER["DOCUMENT_ROOT"]."/modules/templates/templates_files/".'*.php') as $file) {
			$templates[] = basename($file);
	}
	$loop1 = $templates;
	foreach ($loop1 as $url){
		$compentKoppel = db_pdo_fetch_array(db_pdo_select("SELECT * FROM templates WHERE name = '{$url}'"));
		if ($compentKoppel["id"] == ""){
			if (!in_array($url,$skipTemplates)){
				//db_pdo_select("insert into templates (name)values (:name) ",array(":name" => $url));
			}
		}
	}
	$compentKoppel = db_pdo_select_all("SELECT * FROM templates");
	foreach ($compentKoppel as $v){
		if (!in_array($v["name"],$templates)){
				db_pdo_select("delete from templates where name = :name",array(":name" => $v["name"]));
		}
	}

	$nr_templates = '';
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/modules/templates/templates_files/'.$id.'.php')) {
		ob_start();
		include $_SERVER['DOCUMENT_ROOT'].'/modules/templates/templates_files/'.$id.'.php';
		$s = ob_get_contents();
		ob_end_clean();

		return $s;
	} else {
		if ($id != ""){
		$myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/modules/templates/templates_files/'.$id.'.php', "w") or die("Unable to open file!");
		$txt = "<?php
		include(\"header.php\");
		global \$arginfo;
		?>
		<div class=\"container\">
				<h1>Hi Sexy</h1>
				<h2>This seems to be a new template. Add some code and love</h2>
				<br><br>
				<table class=\"table\">
					<?php
						if(is_array(\$arginfo)){ foreach(\$arginfo as \$k => \$v){ ?>
					<tr>
						<td><?php echo \$k?></td><td><?php echo \$v?></td>
					</tr>
				<?php }}?>
				</table>
		</div>
		<?php include(\"footer.php\")?>";
		fwrite($myfile, $txt);
		fclose($myfile);
		//template aanmaken
		templates_load_by_id($id);
	}
	}
}

?>
