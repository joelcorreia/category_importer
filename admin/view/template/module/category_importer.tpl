<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" id="form_category_importer" enctype="multipart/form-data">
  <fieldset>
	<legend><?php echo $text_import_or_sincronize_title; ?></legend>
    <table border="0px" width="100%">
    	<thead>
    	<tr>
    		<td>
				<?php echo $text_csv_example_title; ?>
			</td>
		</tr>
		</thead>
		<tbody>
		<tr>
    		<td>
			<textarea rows="5" cols="80" id="categories" name="categories">
Components
Components -> Mice and Trackballs
Components -> Monitors
Components -> Monitors -> test 1
				</textarea>
		    </td>
    	<tr>
    		<td>
				<input type="submit" value="<?php echo $text_upload_file; ?>" />
		    </td>
    	<tr>
    	<tr>
    		<td>
				<?php echo $observations; ?>
		    </td>
    	<tr>
    	</tbody>
    	<tfoot>
		    <tr>
		        <td style="vertical-align: middle;">
		        	<?php echo $entry_version_status ?>
	        	</td>
		    </tr>
        </tfoot>
    </table>

    </fieldset>
    </form>
    <br>
    <fieldset>
		<legend><?php echo $text_export_title; ?></legend>
		<a href="<?php echo $export_csv; ?>"><?php echo $text_download_csv_title; ?></a>
		<span style="font-size: 10px"> <?php echo $text_download_csv_observations; ?></span>
	</fieldset>

  </div>
</div>
<?php echo $footer; ?>