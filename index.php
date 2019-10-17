<?php

function get_md_intro($str) {
	return preg_replace('/\n/',"\n>\n> ", $str );
}

function get_md($str) {
	return preg_replace('/\n/',"\n\n", $str );
}

function get_content( $out, $chapters ) {
	global $book_id;
	$size = count( $chapters -> chapter );
	for( $i = 0; $i < $size; $i ++ ) {
		if( ! file_exists( './ori/book'. $book_id . '/volume' . $chapters -> id . '/chapter' . $chapters -> chapter[ $i ] -> id . '/content.json' ) ) {
			echo "!! 未找到" . $chapters -> chapter[ $i ] -> title . "的内容，已跳过\n";
			continue;
		}

		fprintf( $out, "### %s\n\n", $chapters -> chapter[ $i ] -> title );
		$content = json_decode( file_get_contents( './ori/book'. $book_id . '/volume' . $chapters -> id . '/chapter' . $chapters -> chapter[ $i ] -> id . '/content.json' ) );
		foreach( $content as $type => $text ) {
			if( $text -> type == 0 ) 
				fprintf( $out, "%s\n\n", get_md( $text -> value ) );
			else {
				$text = explode( '/', $text -> value );
				fprintf( $out, "![](../ori/book%s/volume%s/chapter%s/%s)\n\n", 
					$book_id, $chapters -> id, $chapters -> chapter[ $i ] -> id, $text[ count( $text ) - 1 ] );
			}
		} 
	}
}

function download( $url ) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:67.0) Gecko/20100101 Firefox/67.0');
	return curl_exec( $curl );
}

function get_cache( $dire, $url, $info ) {
	echo "# 查找" . $info . " 缓存中...\n";
	if( file_exists( $dire ) ) {
		echo "## 取得缓存\n";
		return file_get_contents( $dire );
	}
	else {
		echo '## 未找到缓存，正在下载' . $info . "..\n" ;
		$down = download( $url );
		fprintf( fopen( $dire, "w" ), "%s", download( $url ) );
		echo "完成\n\n";
		return $down;
		
	}
}

$book_id = -1;

$books = explode( "\n", `ls ./ori` );
$book_cnt = count( $books );

for( $o = 0; $o < $book_cnt; $o ++ ) {
	$book_id = substr( $books[ $o ], 4 );
	if( $book_id == null ) 
		break;

	$book_info = get_cache( './ori/book' . $book_id . '/book.json', 'https://poi.iqing.com/book/' . "$book_id" . '/api/', '书本信息' );
	$chapter_info = get_cache( './ori/book' . $book_id . '/chapter.json', 'https://poi.iqing.com/book/' . "$book_id" . '/chapter/', '章节信息' );

	echo "# 正在导出 Markdown...\n";

	echo "## 导出书本本信息...\n";

	$book_info = json_decode( $book_info );
	$chapter_info = json_decode( $chapter_info );
	$all_in_one = fopen( './out/' . $book_info -> title . '.md', "w" );

	$book_info -> cover = explode( '/', $book_info -> cover );
	$book_info -> cover = $book_info -> cover[ count( $book_info -> cover ) - 1 ];
	fprintf( $all_in_one, "# " . $book_info -> title . "\n\n" );
	fprintf( $all_in_one, "![](../ori/book%s/%s)\n\n", $book_id, $book_info -> cover );
	fprintf( $all_in_one, "> " . get_md_intro( $book_info -> intro ) . "\n\n" );
	fprintf( $all_in_one, "<center> Author: " . $book_info -> author_name . "</center>\n\n" );

	echo "完成\n\n";

	echo "## 正在导出内容...\n";

	$chapter_info -> count = count( $chapter_info -> results );
	for( $i = 0; $i < $chapter_info -> count; $i ++  ) {
		fprintf( $all_in_one, "## " . $chapter_info -> results[ $i ] -> title . "\n\n" );
		get_content( $all_in_one, $chapter_info -> results[ $i ] );	
	}

	echo "完成\n\n";

	echo '「' . $book_info -> title . "」已经完成导出\n\n";
}
