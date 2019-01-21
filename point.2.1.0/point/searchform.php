<form method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url() ); ?>" _lpchecked="1">
	<fieldset>
		<input type="text" name="s" id="s" value="<?php esc_html_e( 'Search the site', 'point' ); ?>" onblur="if (this.value == '') {this.value = '<?php esc_html_e( 'Search the site', 'point' ); ?>';}" onfocus="if (this.value == '<?php esc_html_e( 'Search the site', 'point' ); ?>') {this.value = '';}" >
		<button id="search-image" class="sbutton" type="submit" value="">
			<i class="point-icon icon-search"></i>
		</button>
	</fieldset>
</form>
