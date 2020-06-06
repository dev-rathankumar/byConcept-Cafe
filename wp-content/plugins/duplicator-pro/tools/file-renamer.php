<?php defined("ABSPATH") or die(""); ?>
<head>
	<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
</head>
<body>
	<?php
	$test_mode = false;

	if (!isset($_POST['action']))
	{
		?>

		<h1>Snap Creek File Rename Utility v0.1</h1>
		This tool will rename all files with '#UNNNN' in their names to the proper UTF-8 characters.<br/><br/>
		Click 'Preview' to see files that will be renamed without performing the rename.<br/><br/>
		Click 'Rename' to rename the files.<br/><br/>

		<form method="post">
			<input id="action" type="hidden" name="action" value="preview" />
			<input style="float:left; margin-right:20px;" type="submit" value="Rename" onclick="jQuery('#action').attr('value', 'rename');
	            return true;"/>
			<input type="submit" value="Preview" />
		</form>

		<?php
	}
	else
	{
		$rename = $_POST['action'] === 'rename';

		if ($rename)
		{
			$text = 'Renaming Files';
		}
		else
		{
			$text = 'Previewing Files';
		}

		echo "<h1>$text</h1>";
		function dirToArray($dir)
		{
			$directoryIterator = new RecursiveDirectoryIterator($dir);
			$recursiveIteratorIterator = new RecursiveIteratorIterator($directoryIterator);
			$regexIterator = new RegexIterator($recursiveIteratorIterator, '/\#U/', RecursiveRegexIterator::GET_MATCH);

			$array = iterator_to_array($regexIterator, true);

			return array_keys($array);
		}

		$dirpath = dirname(__FILE__);
		$filepaths = array();
		$paths = dirToArray($dirpath);

		foreach ($paths as $path)
		{
			if (is_file($path))
			{
				$filepaths[] = $path;
			}
		}

		$file_count = 0;
		asort($filepaths);
		foreach ($filepaths as $filepath)
		{
			if ((strpos($filepath, '#U') !== false) && (strpos($filepath, '.orig') === false))
			{
				$file_count++;
				echo "FILE $filepath<br/>";

				$new_filepath = html_entity_decode(preg_replace("/\#U([0-9A-Fa-f]{4})/", "&#x\\1;", $filepath), ENT_NOQUOTES, 'UTF-8');

				if ($rename)
				{
					//$new_filename = preg_replace("/\#U([0-9A-Fa-f]{4})/", "&#x\\1;", $filepath);

					$backup_filepath = $filepath . '.orig';
					$backup_base = basename($backup_filepath);

					if ($test_mode)
					{
						$copied = true;
					}
					else
					{
						$copied = copy($filepath, $backup_filepath);
					}

					if ($copied)
					{
						if ($test_mode)
						{
							$renamed = true;
						}
						else
						{
							$renamed = @rename($filepath, $new_filepath);
						}

						$rename_base = basename($new_filepath);

						if ($renamed)
						{
							echo "Renamed to $rename_base<br/>";
						}
						else
						{
							echo "***ERROR: Couldn't Rename to $rename_base<br/>";
							@unlink($backup_filepath);
						}
					}
					else
					{
						echo "***ERROR: Problem backing to $backup_base<br/>";
					}
				}
				echo '<br/>';
			}
		}
		echo "<br/>Total Files: $file_count<br/><br/>";

		if (!$rename && ($file_count > 0))
		{
			?>
			<form method="post">
				<input id="action" type="hidden" name="action" value="rename" />
				<input style="float:left; margin-right:20px;" type="submit" value="Proceed with Rename"/>
			</form>
		<?php
	}
}
echo '</body>';
?>
