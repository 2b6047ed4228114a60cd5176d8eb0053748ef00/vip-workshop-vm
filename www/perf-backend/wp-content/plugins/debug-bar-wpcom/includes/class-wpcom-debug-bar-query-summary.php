<?php

class WPCOM_Debug_Bar_Query_Summary extends Debug_Bar_Panel {
	function init() {
		$this->title( __('Query Summary', 'debug-bar') );
	}

	function prerender() {
		$this->set_visible( true );
	}

	function render() {
		global $wpdb, $__query_inspector_explain;
		$query_types = array();
		$query_type_counts = array();
		if ( is_array( $wpdb->queries ) ) {
			$count = count( $wpdb->queries );

			for ( $i = 0; $i < $count; ++$i ) {
				$query = isset( $wpdb->queries[$i]['query'] ) ? $wpdb->queries[$i]['query'] : $wpdb->queries[$i][0];

				$query = trim( preg_replace( '#connection: dbh_.+$#','', $query ) );
				$query = preg_replace( "#\s+#", ' ', $query );
				$query = str_replace( '\"', '', $query );
				$query = str_replace( "\'", '', $query );
				$query = preg_replace( '#wp_\d+_#', 'wp_?_', $query );
				$query = preg_replace( "#'[^']*'#", "'?'", $query );
				$query = preg_replace( '#"[^"]*"#', "'?'", $query );
				$query = preg_replace( "#in ?\([^)]*\)#i", 'in(?)', $query);
				$query = preg_replace( "#= ?\d+ ?#", "= ? ", $query );
				$query = preg_replace( "#\d+(, ?)?#", '?\1', $query);
				$query = preg_replace( "#\s+#", ' ', $query );

				if ( !isset( $query_types[$query] ) )
					$query_types[$query] = 0;

				if ( !isset( $query_type_counts[$query] ) )
					$query_type_counts[$query] = 0;

				$query_type_counts[$query]++;
				$query_types[$query] += isset( $wpdb->queries[$i]['elapsed'] ) ? $wpdb->queries[$i]['elapsed'] : $wpdb->queries[$i][1];
			}
		}
	
		arsort( $query_types );
		$query_time = array_sum( $query_types );
		$out = '<pre style="overflow:auto;">';
		$count = 0;
		$max_time_len = 0;
		$did_qcount_update = false;

		foreach( $query_types as $q => $t ) {
			$count++;
			if ( $query_time )
				$query_time_pct = ( $t / $query_time );
			$max_time_len = max($max_time_len, strlen(sprintf('%0.2f', $t * 1000)));
			if ( $query_time_pct >= .3 ) 
				$color = "red";
			else if ( $query_time_pct >= .1 )
				$color = "orange";
			else
				$color = "green";

			if ( isset( $__query_inspector_explain ) && isset( $__query_inspector_explain[$q] ) )
				$explain = '<br/><a style="padding-left:100px;" onClick="jQuery(\'#explain-'.$count.'\').toggle(); return false;" href="#">toggle explain</a><div id="explain-'.$count.'" style="display:none; padding-left:100px; color:black;">'.print_r($__query_inspector_explain[$q], true).'</div>';
			else
				$explain = '';

			if ( !$did_qcount_update && $explain ) {
				$did_qcount_update = true;
				echo '<style type="text/css">#adminqcount { background: #900; }</style>';
			}

			$out .= sprintf(
				"<span style='color:%s;'>%s queries for %sms &raquo; %s</span>%s\r\n",
				$color,
				str_pad( $query_type_counts[$q], 5, ' ', STR_PAD_LEFT ),
				str_pad( sprintf('%0.2f', $t * 1000), $max_time_len, ' ', STR_PAD_LEFT ),
				$q,
				$explain
			);
		}
		$out .= '</pre>';
		echo $out;
	}
}