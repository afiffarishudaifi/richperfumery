<link href="<?= base_url() ?>css/selectize.css" rel="stylesheet" />
<link href="<?= base_url() ?>css/selectize.bootstrap3.css" rel="stylesheet" />

	<p><b>Group</b></p>
	<!--<select class="selectize " style="width: 100%"  name="kategori"  required>
		<?php foreach($kategori as $al){?>
			<option><?=$al['kategori']?></option>
		<?php }?>
	</select>-->
	<select class="selectize " style="width: 100%"  name="kategori"  required>
		<option value="">kategori</option>
	</select>
                        
<script src="<?= base_url() ?>js/standalone/selectize.min.js"></script>
<script>
	

$(document).ready(function () {
	$('.selectize').selectize({
		    create: true,
		    sortField: 'text'
	});
	
});
            
            
</script>