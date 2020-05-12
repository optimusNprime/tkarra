<?php

/**
 *
 * Import/export page
 *
 * @package Media Boxes by castlecode
 * @author  castlecode
 * 
 */

	/* ### GET DATA FROM DB ### */

	$portfolios = get_option( MEDIA_BOXES_PREFIX . '_portfolios' );
	$skins 		= get_option( MEDIA_BOXES_PREFIX . '_skins' );
	
	if(is_array($portfolios) == false){
		$portfolios = array();
	}

	if(is_array($skins) == false){
		$skins = array();
	}

?>

<div class="media_boxes_options_page_loader"><span class="fa fa-cog fa-spin fa-3x fa-fw"></span></div>

<div class="media_boxes_options_page media_boxes_admin">

	<div class="media_boxes_admin_title">
		<img style="height:40px;" src="<?php echo MEDIA_BOXES_URI; ?>/admin/includes/images/media-boxes.png" alt="Media Boxes">
		&nbsp;
		Import & Export Settings
	</div>

<!-- ====================================================================== --
      	EXPORT
 !-- ====================================================================== -->	
	
	<div class="grid">
		<div class="col-50p">

			<div class="import_export_content">
				<div class="section_title">
					<span title="Choose the gallery that you wish to export to a json file">
						<i class="fa fa-arrow-circle-up"></i> &nbsp;PORTFOLIOS EXPORTER
					</span>
				</div>
				
				<?php if(count($portfolios) <= 0){ ?>
					<p>No portfolios found to export. You must have at least one portfolio created in order to export it.</p>
				<?php }else{ ?>
					<form method="post" action="">
						<input type="hidden" name="action" value="media_boxes_export_portfolios">

						<div class="grid">
							<?php foreach ($portfolios as $row) { ?>
								<div class="col-33p">
									<label for="item_<?php echo $row['uniqid']; ?>" style="margin:0;width: 100% !important;">
										<div class="item">
											<input type="checkbox" name="item_<?php echo $row['uniqid']; ?>" id="item_<?php echo $row['uniqid']; ?>" checked />
											<?php echo $row['name']; ?>
										</div>
									</label>
								</div>
							<?php } ?>
						</div>

						<div class="form-controls">
							<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Export</button>
						</div>

					</form>
				<?php } ?>
			</div>	

		</div>
		<div class="col-50p">
			
			<div class="import_export_content">
				<div class="section_title">
					<span title="Choose the gallery that you wish to export to a json file">
						<i class="fa fa-arrow-circle-up"></i> &nbsp;SKINS EXPORTER
					</span>
				</div>
				
				<?php if(count($skins) <= 0){ ?>
					<p>No skins found to export. You must have at least one skin created in order to export it.</p>
				<?php }else{ ?>
					<form method="post" action="">
						<input type="hidden" name="action" value="media_boxes_export_skins">

						<div class="grid">
							<?php foreach ($skins as $row) { ?>
								<div class="col-33p">
									<label for="item_<?php echo $row['uniqid']; ?>" style="margin:0;width: 100% !important;">
										<div class="item">
											<input type="checkbox" name="item_<?php echo $row['uniqid']; ?>" id="item_<?php echo $row['uniqid']; ?>" checked />
											<?php echo $row['name']; ?>
										</div>
									</label>
								</div>
							<?php } ?>
						</div>

						<div class="form-controls">
							<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Export</button>
						</div>

					</form>
				<?php } ?>
			</div>	

		</div>
	</div>
	
	

	<br><br>

<!-- ====================================================================== --
      	IMPORT
 !-- ====================================================================== -->		

 	<div class="grid">
		<div class="col-50p">

			<div class="import_export_content">
				<div class="section_title">
					<span title="Upload a json file in order to import the settings">
						<i class="fa fa-arrow-circle-down"></i> &nbsp;PORTFOLIOS IMPORTER
					</span>
				</div>

				<form method="post" action="" enctype="multipart/form-data">
					<input type="hidden" name="action" value="media_boxes_import_portfolios">

					<p>
						<input type="file" name="import_file"/>
					</p>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Import</button>
					</div>

				</form>
			</div>	

		</div>
		<div class="col-50p">

			<div class="import_export_content">
				<div class="section_title">
					<span title="Upload a json file in order to import the settings">
						<i class="fa fa-arrow-circle-down"></i> &nbsp;SKINS IMPORTER
					</span>
				</div>

				<form method="post" action="" enctype="multipart/form-data">
					<input type="hidden" name="action" value="media_boxes_import_skins">

					<p>
						<input type="file" name="import_file"/>
					</p>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Import</button>
					</div>

				</form>
			</div>	

		</div>
	</div>

</div>