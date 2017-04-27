<div class="form form-inline">
	<span class="input-group">
		<span class="input-group-addon" id="tabs-operator-compile-build-type">编译选项:</span>
		<select id="compile_versions_ext_option" class="form-control compile_options" aria-describedby="tabs-operator-compile-build-type" style="height: auto;">
			<option value="Debug" selected="selected">Debug版本</option>
			<option value="RelWithDebInfo">带调试信息的Release版本(推荐)</option>
			<option value="Release">Release版本</option>
			<option value="MinSizeRel">极限压缩优化(Release)版本</option>
		</select>
	</span>
	<div>
		<h6>附加编译选项:<span id="compile_versions_ext_option_desc" class="label label-info"></span></h6>
	</div>
</div>
<br />

<script type="text/javascript">
jQuery(document).ready(function(){
	var compile_build_type_func = (function(){
		var build_type = $("#compile_versions_ext_option").val().toLowerCase();
		var msg_dom = $("#compile_versions_ext_option_desc");
		var all_build_params = '-std=gnu++11 -Wall -Werror -rdynamic -fPIC -D_FILE_OFFSET_BITS=64 -D__STDC_FORMAT_MACROS -fno-omit-frame-pointer';
		if ( 'debug' == build_type ) {
			msg_dom.html(all_build_params + ' -g -ggdb -O0');
		} else if( 'relwithdebinfo' == build_type ){
			msg_dom.html(all_build_params + ' -O2 -g -DNDEBUG -ggdb');
		} else if( 'release' == build_type ){
			msg_dom.html(all_build_params + ' -O3 -DNDEBUG');
		} else if( 'minsizerel' == build_type ){
			msg_dom.html(all_build_params + ' -Os -DNDEBUG');
		} else {
			msg_dom.html(all_build_params);
		}
	});

	$("#compile_versions_ext_option").change(compile_build_type_func);
	compile_build_type_func();
});
</script>
<?php
