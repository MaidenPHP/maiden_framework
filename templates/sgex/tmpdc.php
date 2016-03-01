<?php
class Template {
	static function buttonsubmit( $value, $img="" ){
		if( $img=="" ) return "<input type='submit' value='$value'>";
		else return "<input type='image' src='".IBTN."$img' value='$value' onclick='this.form.submit()'>";
	}
	
	static function button( $value, $attr="type='button'" ){
		return "<span class='art-button-wrapper'>
			<span class='art-button-l'> </span><span class='art-button-r'> </span>
			<input value='$value' class='art-button' style='zoom: 1;' $attr></span>";
	}
	
	static function buttondisabled( $img="" ){
		if( $img=="" ) return "";
		return "<img src='".IBTN."$img'>";
	}
	
	static function link( $type, $val ){
		if( $type=="img" ){
			return "<img src='".IBTN."$val'>";
		} else {
			return $val;	
		}
	}
}
?>